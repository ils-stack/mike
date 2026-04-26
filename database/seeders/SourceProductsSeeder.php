<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceProductsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = $this->decodedRows();

        DB::table('ss_products')->upsert(
            $rows,
            ['id'],
            ['product_name', 'active', 'created_at', 'updated_at', 'nosplit', 'comm_summ']
        );
    }

    private function decodedRows(): array
    {
        $json = gzdecode(base64_decode(
            'H4sIAAAAAAAAE5WWX2vbMBTFv4rwcw26smQ7emvXdSusTVno9jBG0RytMfOf4Njbwth333W8tk4kWyqEQJJD/OPcc4/05U+QrwMJZ8G2qddd1j5UqtSBDD7k33TT7oOzQGVt/lMfNFmjVavXD6pFBaOMhTQNWUwolyySQqC6265PJYuQMgKxjCLJU5RU9W5b5Pg7xX+sy/Jh15Ulfvp7NsAwA+ZGV62q6p+KhOT8+mblTSWoZKmFKgophOxAxVDFp6ngiSoyqKqPWmWbEQudZ+ESYisL0JBRQoWMmKSJh0PcYHlXqCzXjZ8xgE8CCeAwJpYi8jBGGDDnVbapG7JaXrknNYRD9PlhC0d+uIxiD6DYALrVv8gdfjWiYTM0+Ch0yEWTzObmeVaJaU/X1I2az81hDlQQmkhYSGDODEfMgyU1N6sucbe6cn5OxzTMtucowVdCgEmO5s0E55lmYSmd7/rNPiv0a8zhtqXiIbAQEkKxc3BQC4/YADV47va/852HNRARyqQASSesOZU4WcxCvq+8ptQ/R/SrImyZGWzBBqASUkln2vgFxazjHgWbeHmOb3d1XfhaJPq9OhScyUXxBX16cKKUjrlggsss5PEx4VWI4xUSM8M7ljgdM+v5hexp4RDxs1ZFu3kNIUwQJk8lCoPESWjp7KIr3Z0UhywlEGEPY/851y7y6Ucw6/rZI98hDlg4HoYW2C06lTgtMnt7HK/V/rHKlQ/Yot97jn5Ek2BHEieYWeL/72rkqisKJ1LS7z8GhdLhNLXG6VjiHKHZ5O9XqzJvN+QCAX50Wx8qPDuwz7nkExM8lTivkWafX779lBcFJmr3ejAx1NIcmDhprikwS7nf3t+Q5eX1lWsDcb0wx/1tEnPs2sB0uPw6eSYavr9s388bNOptDpLbrkwWiavamVnty62u8uqRXKhCVZmepxIh4P2A96ccY8Phf0plkUza9PUfHcc4tSUNAAA='
        ));

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
