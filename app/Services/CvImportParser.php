<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class CvImportParser
{
    public function parse(string $path): array
    {
        $mime = mime_content_type($path) ?: '';
        $text = '';

        if ($mime === 'application/pdf') {
            try {
                $document = (new Parser())->parseFile($path);
                $text = preg_replace('/\r/', "\n", $document->getText()) ?? '';
            } catch (\Throwable $e) {
                $text = '';
            }

            if (mb_strlen(trim($text)) < 40) {
                $text = $this->ocrPdf($path);
            }
        } elseif (str_starts_with($mime, 'image/')) {
            $text = $this->ocrImage($path);
        }

        $text = preg_replace('/[\n]{3,}/', "\n\n", $text) ?? $text;

        $email = $this->find('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text);
        $phone = $this->find('/(?:\+62|62|0)[0-9][0-9\s().-]{8,14}/', $text);
        $phone = $phone ? preg_replace('/[^0-9+]/', '', $phone) : null;

        $linkedin = $this->find('/https?:\/\/(?:www\.)?linkedin\.com\/[^\s|]+/i', $text);
        $instagram = $this->find('/https?:\/\/(?:www\.)?instagram\.com\/[^\s|]+/i', $text);
        $portfolio = $this->find('/https?:\/\/[^\s|]+/i', $text);

        if ($portfolio && preg_match('/linkedin|instagram/i', $portfolio)) {
            $portfolio = null;
        }

        $lines = collect(preg_split('/\n+/', $text))
            ->map(fn ($line) => trim(preg_replace('/\s{2,}/', ' ', $line)))
            ->filter()
            ->values();

        $name = $lines->first(function ($line) use ($email, $phone) {
            if (strlen($line) < 3 || strlen($line) > 80) return false;
            if ($email && str_contains(strtolower($line), strtolower($email))) return false;
            if ($phone && str_contains($line, $phone)) return false;
            if (preg_match('/(@|curriculum vitae|resume|profile|data diri|contact)/i', $line)) return false;
            return preg_match('/^[\pL][\pL\s.\'’-]+$/u', $line);
        }) ?? null;

        $ringkasan = $this->ringkasan($text);
        $pendidikan = $this->pendidikan($lines);
        $skills = $this->skills($text);

        $jenisKelamin = preg_match('/jenis\s*kelamin|gender/i', $text)
            ? (preg_match('/\b(perempuan|wanita|female|P)\b/i', $text) ? 'P' : (preg_match('/\b(laki-laki|laki laki|pria|male|L)\b/i', $text) ? 'L' : null))
            : null;

        $tempatLahir = $this->labelValue($text, 'tempat\s+(lahir|kelahiran)|place\s+of\s+birth');
        $tglLahir = $this->tanggalLahir($text);
        $kota = $this->labelValue($text, 'kota|domisili|city');
        $alamat = $this->labelValue($text, 'alamat|address');

        return [
            'name' => $name,
            'email' => $email,
            'no_telepon' => $phone,
            'jenis_kelamin' => $jenisKelamin,
            'tempat_lahir' => $tempatLahir,
            'tgl_lahir' => $tglLahir,
            'kota' => $kota,
            'alamat' => $alamat,
            'ringkasan' => $ringkasan,
            'linkedin_url' => $linkedin,
            'instagram_url' => $instagram,
            'portofolio_url' => $portfolio,
            'pendidikan' => $pendidikan,
            'skills' => $skills,
            'pengalaman' => [],
            'organisasi' => [],
            'proyek' => [],
            'teks_cv' => mb_substr($text, 0, 12000),
        ];
    }

    private function find(string $pattern, string $text): ?string
    {
        return preg_match($pattern, $text, $match) ? rtrim($match[0], ".,;| ") : null;
    }

    private function ringkasan(string $text): ?string
    {
        if (! preg_match('/(?:profile|profil|summary|ringkasan|tentang saya)\s*(?:\n|:)(.{80,700}?)(?:\n\n|\n[A-Z][A-Z\s]{3,}\n)/is', $text, $match)) {
            return null;
        }

        return mb_substr(trim(preg_replace('/\s+/', ' ', $match[1])), 0, 300);
    }

    private function pendidikan($lines): array
    {
        $hasil = [];

        foreach ($lines as $index => $line) {
            if (! preg_match('/(universitas|institut|politeknik|sekolah|sma|smk|d3|d4|s1|s2|s3)/i', $line)) {
                continue;
            }

            $tahun = null;
            if (preg_match('/(19|20)\d{2}\s*(?:-|–|—)\s*(?:(19|20)\d{2}|sekarang)/i', $line, $match)) {
                $tahun = $match[0];
            } elseif (preg_match('/(19|20)\d{2}/', $line, $match)) {
                $tahun = $match[0];
            }

            $jenjang = null;
            foreach (['S3', 'S2', 'S1', 'D4', 'D3', 'SMA/SMK'] as $item) {
                if (preg_match('/\b' . preg_quote($item, '/') . '\b/i', $line)) {
                    $jenjang = $item;
                    break;
                }
            }

            $institusi = mb_substr($line, 0, 150);
            if (filled($institusi)) {
                $hasil[] = compact('jenjang', 'institusi', 'tahun');
            }
        }

        return collect($hasil)->unique('institusi')->take(5)->values()->all();
    }

    private function skills(string $text): array
    {
        $daftar = ['PHP', 'Laravel', 'JavaScript', 'TypeScript', 'Python', 'Java', 'Kotlin', 'C++', 'C#', 'SQL', 'MySQL', 'PostgreSQL', 'HTML', 'CSS', 'Tailwind', 'React', 'Vue', 'Node.js', 'Git', 'Docker', 'Excel', 'Figma', 'Canva', 'Communication', 'Leadership', 'Teamwork', 'Problem Solving'];
        $hasil = [];

        foreach ($daftar as $skill) {
            if (preg_match('/\b' . preg_quote($skill, '/') . '\b/i', $text)) {
                $hasil[] = $skill;
            }
        }

        return $hasil;
    }

    private function ocrPdf(string $pdfPath): string
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'era-cv-' . bin2hex(random_bytes(6));
        mkdir($directory, 0775, true);

        $popplerMatches = glob((getenv('LOCALAPPDATA') ?: '') . '\\Microsoft\\WinGet\\Packages\\oschwartz10612.Poppler*\\poppler-*\\Library\\bin\\pdftoppm.exe') ?: [];

        $pdftoppm = $this->findExecutable([
            env('CV_OCR_POPPLER_PDFTOPPM'),
            $popplerMatches[0] ?? null,
            'C:\\Users\\vina dwi\\AppData\\Local\\Microsoft\\WinGet\\Packages\\oschwartz10612.Poppler_Microsoft.Winget.Source_8wekyb3d8bbwe\\poppler-26.09.0\\Library\\bin\\pdftoppm.exe',
            'pdftoppm',
        ]);

        if (! $pdftoppm) {
            throw new \RuntimeException('Konverter PDF OCR belum tersedia.');
        }

        $prefix = $directory . DIRECTORY_SEPARATOR . 'page';
        $command = escapeshellarg($pdftoppm)
            . ' -jpeg -r 300 '
            . escapeshellarg($pdfPath)
            . ' ' . escapeshellarg($prefix);
        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            $this->hapusDirektori($directory);
            throw new \RuntimeException('PDF gagal dikonversi untuk OCR.');
        }

        $pages = glob($directory . DIRECTORY_SEPARATOR . 'page-*.jpg') ?: [];
        sort($pages, SORT_NATURAL);

        $text = '';
        foreach ($pages as $page) {
            $text .= "\n" . $this->ocrImage($page);
        }

        $this->hapusDirektori($directory);
        return $text;
    }

    private function ocrImage(string $imagePath): string
    {
        $tesseract = $this->findExecutable([
            env('CV_OCR_TESSERACT'),
            'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'tesseract',
        ]);

        if (! $tesseract) {
            throw new \RuntimeException('Tesseract OCR belum tersedia.');
        }

        $customTessdata = base_path('storage/app/tessdata');
        $language = is_file($customTessdata . DIRECTORY_SEPARATOR . 'ind.traineddata')
            ? 'ind+eng'
            : 'eng';

        $tessdataOption = is_dir($customTessdata)
            ? ' --tessdata-dir ' . escapeshellarg($customTessdata)
            : '';

        $command = escapeshellarg($tesseract)
            . ' ' . escapeshellarg($imagePath)
            . ' stdout -l ' . escapeshellarg($language)
            . $tessdataOption
            . ' --psm 6';

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException('Gambar CV gagal dibaca oleh OCR.');
        }

        return implode("\n", $output);
    }

    private function findExecutable(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (! filled($candidate)) {
                continue;
            }

            if (str_contains($candidate, DIRECTORY_SEPARATOR) && is_file($candidate)) {
                return $candidate;
            }

            if (! str_contains($candidate, DIRECTORY_SEPARATOR)) {
                $found = trim((string) shell_exec('where ' . escapeshellarg($candidate) . ' 2>NUL'));
                if (filled($found)) {
                    $first = preg_split('/\r?\n/', $found)[0] ?? null;
                    if ($first && is_file($first)) {
                        return $first;
                    }
                }
            }
        }

        return null;
    }

    private function hapusDirektori(string $directory): void
    {
        foreach (glob($directory . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($directory);
    }

    private function labelValue(string $text, string $labelPattern): ?string
    {
        if (! preg_match('/(?:' . $labelPattern . ')[^:\-]*[:\-]?\s*([^\n]{1,120})/iu', $text, $match)) {
            return null;
        }

        $value = trim($match[1]);
        $value = preg_replace('/\s{2,}/', ' ', $value);

        return filled($value) ? mb_substr($value, 0, 500) : null;
    }

    private function tanggalLahir(string $text): ?string
    {
        if (! preg_match('/(?:tgl(?:\.?)\s*lahir|tanggal\s*lahir|date\s+of\s+birth)[^\n]*[:\-]?\s*(\d{1,2}[\/-]\d{1,2}[\/-]\d{4}|\d{4}[\/-]\d{1,2}[\/-]\d{1,2}|\d{1,2}\s+[A-Za-z]+\s+\d{4})/iu', $text, $match)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse(trim($match[1]))->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
