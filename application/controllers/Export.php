<?php
defined('BASEPATH') or exit('No direct script accesss allowed');
date_default_timezone_set('Asia/Jakarta');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends CI_Controller
{
    public function export_data()
    {
        $periode = $this->input->get('periode');
        $create_date = date_create($periode);
        $month = date_format($create_date, 'm');
        $bulan = date_format($create_date, 'F');
        $year = date_format($create_date, 'Y');

        $penghuni_aktif = $this->app->query_all_data_penghuni(2)->result();
        $pengeluaran = $this->db->where([
            'month(tanggal)' => $month,
            'year(tanggal)' => $year
        ])
            ->order_by('tanggal', 'ASC')
            ->get('pengeluaran')->result();

        $setoran = $this->db->where([
            'month(tanggal)' => $month,
            'year(tanggal)' => $year
        ])
            ->order_by('tanggal', 'ASC')
            ->get('setoran')->result();
        $show_periode = 'Periode: ' . $bulan . ' ' . $year;

        $spreadsheet = new Spreadsheet();

        $styleTitle = [
            'font' => [
                'name' => 'Arial Rounded MT Bold'
            ]
        ];
        $activeWorksheet = $spreadsheet->getActiveSheet();

        //set title on header
        $activeWorksheet->getStyle('A1:A2')->applyFromArray($styleTitle);
        $activeWorksheet->getStyle('A1')->getFont()->setSize(20);
        $activeWorksheet->getStyle('A2')->getFont()->setSize(11);
        $activeWorksheet->setCellValue('A1', 'Laporan Bulanan Keuangan Kost');
        $activeWorksheet->setCellValue('A2', "$show_periode");
        //end title

        //style body universal
        $styleBodyUniversal = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'name' => 'Arial',
                'size' => 11
            ]
        ];
        //end body universal















        //set heading table pendapatan
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => '5c5c5c']
            ],
            'font' => [
                'color' => ['rgb' => 'ffffff'],
                'name' => 'Arial',
                'bold' => true,
                'size' => 12
            ]
        ];
        $styleHeadPendapatan = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => '9BBB59']
            ],
            'font' => [
                'color' => ['rgb' => 'ffffff'],
                'name' => 'Arial',
                'size' => 11
            ]
        ];

        $activeWorksheet->getStyle('A4:I4')->applyFromArray($styleArray);
        $activeWorksheet->mergeCells('A4:I4');
        $activeWorksheet->setCellValue('A4', 'Pendapatan');

        $activeWorksheet->getStyle('A5:I5')->applyFromArray($styleHeadPendapatan);
        $activeWorksheet->setCellValue('A5', '#');
        $activeWorksheet->setCellValue('B5', 'Tgl Seharusnya Bayar');
        $activeWorksheet->setCellValue('C5', 'Tgl Bayar');
        $activeWorksheet->setCellValue('D5', 'No Kamar');
        $activeWorksheet->setCellValue('E5', 'Nama');
        $activeWorksheet->setCellValue('F5', 'Harga Kamar');
        $activeWorksheet->setCellValue('G5', 'Bayar');
        $activeWorksheet->setCellValue('H5', 'Via');
        $activeWorksheet->setCellValue('I5', 'Ket');
        $activeWorksheet->getStyle('B5')->getFont()->setSize(7.5);
        //end heading table pendapatan
        //body table pendapatan
        $start = 6;
        $new_row = 6;
        $no = 1;
        foreach ($penghuni_aktif as $pa) {
            $pembayaran = $this->db->get_where('pembayaran', [
                'id_penghuni' => $pa->id_penghuni,
                'periode' => $periode
            ])->row();
            $styleBodyPendapatan = [];
            if ($pembayaran) {
                $tgl_bayar = cek_tgl($pembayaran->tgl_bayar);
                $jml_bayar = number_format($pembayaran->jml_bayar);
                $via = $pembayaran->via_pembayaran;
                $ket = $pembayaran->ket;

                $styleBodyPendapatan = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'name' => 'Arial',
                        'size' => 11
                    ]
                ];
            } else {
                $tgl_bayar = '-';
                $jml_bayar = '-';
                $via = '-';
                $ket = '-';
                $styleBodyPendapatan = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'name' => 'Arial',
                        'size' => 11
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFF00']
                    ],
                ];
            }

            $tgl_m = date_create($pa->tgl_penempatan);
            $tgl_bayar_seharusnya = date_format($tgl_m, 'd');


            $activeWorksheet->insertNewRowBefore($new_row + 1, 1);

            $activeWorksheet->setCellValue('A' . $start, "$no")
                ->setCellValue('B' . $start, "$tgl_bayar_seharusnya")
                ->setCellValue('C' . $start, "$tgl_bayar")
                ->setCellValue('D' . $start, "$pa->no_kamar")
                ->setCellValue('E' . $start, "$pa->nama_penghuni")
                ->setCellValue('F' . $start, "$pa->price")
                ->setCellValue('G' . $start, "$jml_bayar")
                ->setCellValue('H' . $start, "$via")
                ->setCellValue('I' . $start, "$ket");

            $activeWorksheet->getStyle('A' . $start . ':I' . $start . '')->applyFromArray($styleBodyPendapatan);
            $start++;
            $no++;
            $new_row++;
        }
        //end table pendapatan

















        //set heading pengeluaran
        $stylePengeluaran = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => '5c5c5c']
            ],
            'font' => [
                'color' => ['rgb' => 'ffffff'],
                'name' => 'Arial',
                'bold' => true,
                'size' => 12
            ]
        ];
        $styleHeadPengeluaran = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'F79646'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'F79646']
            ],
            'font' => [
                'color' => ['rgb' => 'ffffff'],
                'name' => 'Arial',
                'size' => 11
            ]
        ];

        $activeWorksheet->getStyle('L4:P4')->applyFromArray($stylePengeluaran);
        $activeWorksheet->mergeCells('L4:P4');
        $activeWorksheet->setCellValue('L4', 'Pengeluaran');

        $activeWorksheet->getStyle('L5:P5')->applyFromArray($styleHeadPengeluaran);
        $activeWorksheet->setCellValue('L5', '#')
            ->setCellValue('M5', 'Tanggal')
            ->setCellValue('N5', 'Biaya')
            ->setCellValue('O5', 'Nominal')
            ->setCellValue('P5', 'Ket');
        //end heading pengeluaran
        //set data pengeluaran
        $styleBodyPengeluaran = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FDE9D9']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'F79646'],
                ],
            ],
        ];
        $no_pengeluaran = 1;
        $start_pengeluaran = 6;
        $new_row_pengeluaran = 6;
        $total_pengeluaran = 0;
        foreach ($pengeluaran as $p) {
            $total_pengeluaran += $p->nominal;
            $tgl = cek_tgl($p->tanggal);

            $activeWorksheet->insertNewRowBefore($new_row_pengeluaran + 1, 1);
            $activeWorksheet->setCellValue('L' . $start_pengeluaran, "$no_pengeluaran")
                ->setCellValue('M' . $start_pengeluaran, "$tgl")
                ->setCellValue('N' . $start_pengeluaran, "$p->biaya")
                ->setCellValue('O' . $start_pengeluaran, "$p->nominal")
                ->setCellValue('P' . $start_pengeluaran, "$p->ket");
            $activeWorksheet->getStyle('L' . $start_pengeluaran . ':P' . $start_pengeluaran . '')->applyFromArray($styleBodyUniversal);
            $activeWorksheet->getStyle('L' . $start_pengeluaran . ':P' . $start_pengeluaran . '')->applyFromArray($styleBodyPengeluaran);
            $no_pengeluaran++;
            $start_pengeluaran++;
            $new_row_pengeluaran++;
        }
        //end data pengeluaran

        //rekap data pengeluaran
        $jml_data_pengeluaran = count($pengeluaran);
        if ($jml_data_pengeluaran > 1) {
            $space_from_start = $start_pengeluaran;
        } else {
            $space_from_start = $start_pengeluaran;
        }
        $start_data_pengeluaran = $space_from_start;
        $activeWorksheet->mergeCells('L' . $start_data_pengeluaran . ':N' . $start_data_pengeluaran . '');
        $activeWorksheet->mergeCells('O' . $start_data_pengeluaran . ':P' . $start_data_pengeluaran . '');
        $activeWorksheet->getStyle('L' . $start_data_pengeluaran . ':P' . $start_data_pengeluaran . '')->applyFromArray($styleHeadPengeluaran);
        $activeWorksheet->setCellValue('L' . $start_data_pengeluaran, 'Total');
        $activeWorksheet->setCellValue('O' . $start_data_pengeluaran, "$total_pengeluaran");
        //end rekap data pengeluaran
















        //set heading setoran
        $styleSetoran = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => '5c5c5c']
            ],
            'font' => [
                'color' => ['rgb' => 'ffffff'],
                'name' => 'Arial',
                'bold' => true,
                'size' => 12
            ]
        ];
        $styleHeadSetoran = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'C0504D'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'C0504D']
            ],
            'font' => [
                'color' => ['rgb' => 'ffffff'],
                'name' => 'Arial',
                'size' => 11
            ]
        ];

        $activeWorksheet->getStyle('S4:V4')->applyFromArray($styleSetoran);
        $activeWorksheet->mergeCells('S4:V4');
        $activeWorksheet->setCellValue('S4', 'Setoran');

        $activeWorksheet->getStyle('S5:V5')->applyFromArray($styleHeadSetoran);
        $activeWorksheet->setCellValue('S5', '#')
            ->setCellValue('T5', 'Tanggal')
            ->setCellValue('U5', 'Ket')
            ->setCellValue('V5', 'Nominal');
        //end heading setoran
        //set body setoran
        $styleBodySetoran = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'E6B8B7']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'C0504D'],
                ],
            ],
        ];
        $no_setoran = 1;
        $start_setoran = 6;
        $new_row_setoran = 6;
        $total_setoran = 0;
        foreach ($setoran as $s) {
            $total_setoran += $s->nominal;
            $tgl = cek_tgl($s->tanggal);

            $activeWorksheet->setCellValue('S' . $start_setoran, "$no_setoran")
                ->setCellValue('T' . $start_setoran, "$tgl")
                ->setCellValue('U' . $start_setoran, "$s->ket")
                ->setCellValue('V' . $start_setoran, "$s->nominal");
            $activeWorksheet->getStyle('S' . $start_setoran . ':V' . $start_setoran . '')->applyFromArray($styleBodySetoran);
            $activeWorksheet->getStyle('S' . $start_setoran . ':V' . $start_setoran . '')->applyFromArray($styleBodySetoran);
            $no_setoran++;
            $start_setoran++;
            $new_row_setoran++;
        }

        $jml_setoran = count($setoran);
        if ($jml_setoran > 1) {
            $tfoot_setoran = $start_setoran;
        } else {
            $tfoot_setoran = $start_setoran;
        }
        $activeWorksheet->mergeCells('S' . $tfoot_setoran . ':U' . $tfoot_setoran . '');
        $activeWorksheet->setCellValue('S' . $tfoot_setoran, 'Total')
            ->setCellValue('V' . $tfoot_setoran, "$total_setoran");
        $activeWorksheet->getStyle('S' . $tfoot_setoran . ':V' . $tfoot_setoran . '')->applyFromArray($styleHeadSetoran);

        //end body setoran



        //set the header first, so the result will be treated as an xlsx file.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //make it an attachment so we can define filename
        header('Content-Disposition: attachment;filename="hello world.xlsx"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        // $writer->save('hello world.xlsx');
    }
}
