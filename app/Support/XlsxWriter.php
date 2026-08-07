<?php

namespace App\Support;

use ZipArchive;

/**
 * Penulis berkas Excel (.xlsx) sederhana tanpa pustaka pihak ketiga.
 *
 * Cukup untuk kebutuhan laporan tabular: judul, tabel berpembatas, angka
 * berformat, dan penanda status. Berkas ditulis sebagai paket OOXML minimal
 * (workbook + satu lembar kerja + gaya) memakai ekstensi zip bawaan PHP.
 */
class XlsxWriter
{
    /** Indeks gaya sel; nilainya mengacu pada urutan <cellXfs> di styles(). */
    public const BIASA        = 0;
    public const JUDUL        = 1;
    public const SUBJUDUL     = 2;
    public const HEADER       = 3;
    public const TEKS         = 4;   // sel tabel rata kiri
    public const ANGKA        = 5;   // sel tabel rata tengah
    public const SKOR         = 6;   // 0,0000 tebal
    public const LAYAK        = 7;   // hijau
    public const TIDAK_LAYAK  = 8;   // merah
    public const DESIMAL      = 9;   // 0,000
    public const LABEL        = 10;  // teks tebal tanpa pembatas
    public const SOROT        = 11;  // sel tabel berlatar abu, tebal

    /** @var list<string> potongan XML tiap baris <row> */
    private array $baris = [];

    /** @var list<string> rentang sel yang digabung, mis. "A1:H1" */
    private array $gabung = [];

    private int $nomorBaris = 0;

    private string $kolomXml = '';

    public function __construct(private string $namaLembar = 'Sheet1')
    {
    }

    /** Lebar tiap kolom dalam satuan karakter, mulai dari kolom A. */
    public function lebarKolom(array $lebar): static
    {
        $xml = '';
        foreach (array_values($lebar) as $i => $w) {
            $kolom = $i + 1;
            $xml .= '<col min="' . $kolom . '" max="' . $kolom . '" width="' . $w . '" customWidth="1"/>';
        }

        $this->kolomXml = $xml === '' ? '' : '<cols>' . $xml . '</cols>';

        return $this;
    }

    /**
     * Tulis satu baris.
     *
     * Tiap sel boleh berupa nilai polos (gaya BIASA) atau pasangan
     * [nilai, gaya]. Nilai int/float ditulis sebagai angka, selebihnya teks.
     */
    public function tulis(array $sel = []): static
    {
        $this->nomorBaris++;

        $xml = '';
        foreach (array_values($sel) as $i => $isi) {
            [$nilai, $gaya] = is_array($isi) ? [$isi[0], $isi[1] ?? self::BIASA] : [$isi, self::BIASA];

            if ($nilai === null || $nilai === '') {
                continue;
            }

            $ref = self::kolomKe($i + 1) . $this->nomorBaris;

            $xml .= is_int($nilai) || is_float($nilai)
                ? '<c r="' . $ref . '" s="' . $gaya . '"><v>' . $nilai . '</v></c>'
                : '<c r="' . $ref . '" s="' . $gaya . '" t="inlineStr"><is><t xml:space="preserve">'
                    . htmlspecialchars((string) $nilai, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</t></is></c>';
        }

        $this->baris[] = '<row r="' . $this->nomorBaris . '">' . $xml . '</row>';

        return $this;
    }

    /** Gabungkan sel pada baris terakhir, dari kolom A sampai kolom ke-$sampai. */
    public function gabungBarisTerakhir(int $sampai): static
    {
        $this->gabung[] = 'A' . $this->nomorBaris . ':' . self::kolomKe($sampai) . $this->nomorBaris;

        return $this;
    }

    /** Simpan berkas .xlsx ke $path (berkas lama ditimpa). */
    public function simpan(string $path): string
    {
        if (is_file($path)) {
            unlink($path);
        }

        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->relsUtama());
        $zip->addFromString('xl/workbook.xml', $this->workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->relsWorkbook());
        $zip->addFromString('xl/styles.xml', $this->styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheet());

        $zip->close();

        return $path;
    }

    /** Nomor kolom (1) menjadi huruf kolom Excel ("A"). */
    public static function kolomKe(int $nomor): string
    {
        $huruf = '';

        while ($nomor > 0) {
            $sisa   = ($nomor - 1) % 26;
            $huruf  = chr(65 + $sisa) . $huruf;
            $nomor  = intdiv($nomor - $sisa - 1, 26);
        }

        return $huruf;
    }

    private function sheet(): string
    {
        $gabung = $this->gabung === []
            ? ''
            : '<mergeCells count="' . count($this->gabung) . '">'
                . implode('', array_map(fn ($r) => '<mergeCell ref="' . $r . '"/>', $this->gabung))
                . '</mergeCells>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetFormatPr defaultRowHeight="15"/>'
            . $this->kolomXml
            . '<sheetData>' . implode('', $this->baris) . '</sheetData>'
            . $gabung
            . '</worksheet>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . htmlspecialchars(mb_substr($this->namaLembar, 0, 31), ENT_XML1 | ENT_QUOTES, 'UTF-8')
            . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function relsUtama(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function relsWorkbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function styles(): string
    {
        $garis = '<border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right>'
            . '<top style="thin"><color rgb="FFCBD5E1"/></top><bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/></border>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="2"><numFmt numFmtId="164" formatCode="0.0000"/><numFmt numFmtId="165" formatCode="0.000"/></numFmts>'
            . '<fonts count="7">'
            . '<font><sz val="11"/><color rgb="FF1F2937"/><name val="Calibri"/></font>'                 // 0 biasa
            . '<font><b/><sz val="15"/><color rgb="FF0E2650"/><name val="Calibri"/></font>'             // 1 judul
            . '<font><i/><sz val="10"/><color rgb="FF64748B"/><name val="Calibri"/></font>'             // 2 subjudul
            . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'             // 3 header
            . '<font><b/><sz val="11"/><color rgb="FF0E2650"/><name val="Calibri"/></font>'             // 4 tebal gelap
            . '<font><b/><sz val="11"/><color rgb="FF166534"/><name val="Calibri"/></font>'             // 5 hijau
            . '<font><b/><sz val="11"/><color rgb="FF991B1B"/><name val="Calibri"/></font>'             // 6 merah
            . '</fonts>'
            . '<fills count="6">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF14346B"/><bgColor indexed="64"/></patternFill></fill>'  // 2 header
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/><bgColor indexed="64"/></patternFill></fill>'  // 3 hijau muda
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/><bgColor indexed="64"/></patternFill></fill>'  // 4 merah muda
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/><bgColor indexed="64"/></patternFill></fill>'  // 5 abu
            . '</fills>'
            . '<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border>' . $garis . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="12">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="4" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="5" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="6" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }
}
