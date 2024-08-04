<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel extends CI_Controller
{
    public function index()
    {
        $periode_a = $this->input->get('date_a');
        $periode_b = $this->input->get('date_b');
        $kost_id = $this->session->userdata('kost_id');
        $pengeluaran = $this->db->where("tanggal BETWEEN '$periode_a' AND '$periode_b'")
            ->where('id_kost', $kost_id)
            ->order_by('tanggal', 'ASC')
            ->get('pengeluaran')->result();

        $setoran = $this->db->where("tanggal BETWEEN '$periode_a' AND '$periode_b'")
            ->where('id_kost', $kost_id)
            ->order_by('tanggal', 'ASC')
            ->get('setoran')->result();

        $penghuni_aktif = $this->app->query_all_data_penghuni($periode_a, $periode_b)->result();
        $data_kost = $this->db->get_where('kost', ['id' => $kost_id])->row();
        $kost_name = $data_kost->kost_name;


        $sdate_a = cek_tgl($periode_a);
        $sdate_b = cek_tgl($periode_b);
        $show_periode = 'Periode: ' . $sdate_a . ' s/d ' . $sdate_b;


        $spreadsheet = new Spreadsheet();
        $excel = $spreadsheet->getActiveSheet();




        $styleTitle = [
            'font' => [
                'name' => 'Arial Rounded MT Bold'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];
        $excel->mergeCells('A1:I1');
        $excel->mergeCells('A2:I2');
        $excel->mergeCells('A3:I3');

        $excel->getStyle('A1:A3')->applyFromArray($styleTitle);
        $excel->getStyle('A1')->getFont()->setSize(20);
        $excel->getStyle('A2')->getFont()->setSize(13);
        $excel->getStyle('A3')->getFont()->setSize(8);
        $excel->setCellValue('A1', "Laporan Keuangan");
        $excel->setCellValue('A2', "$kost_name");
        $excel->setCellValue('A3', "$show_periode");

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
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'size' => 12,
                'underline' => true
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

        $excel->getStyle('A6:I6')->applyFromArray($styleArray);
        $excel->mergeCells('A6:I6');
        $excel->setCellValue('A6', 'Pendapatan');

        $excel->getStyle('A7:I7')->applyFromArray($styleHeadPendapatan);
        $excel->setCellValue('A7', '#');
        $excel->setCellValue('B7', 'Tgl Seharusnya Bayar');
        $excel->setCellValue('C7', 'Tgl Bayar');
        $excel->setCellValue('D7', 'No Kamar');
        $excel->setCellValue('E7', 'Nama');
        $excel->setCellValue('F7', 'Harga Kamar');
        $excel->setCellValue('G7', 'Bayar');
        $excel->setCellValue('H7', 'Via');
        $excel->setCellValue('I7', 'Ket');
        $excel->getStyle('B7')->getFont()->setSize(7.5);
        //end heading table pendapatan

        //body pendapatan
        $start_pendapatan = 8;
        $nr_pendapatan = 8;
        $no_pendapatan = 1;

        $total_pemasukan_real = 0;
        $total_pemasukan_seharusnya = 0;
        foreach ($penghuni_aktif as $d) {
            //set data pembayaran
            $c_periode_a = date_create($periode_a);
            $c_periode_b = date_create($periode_b);

            $periode_a = date_format($c_periode_a, 'Y-m');
            $periode_b = date_format($c_periode_b, 'Y-m');

            $data_pembayaran = $this->db
                ->where('id_penghuni', $d->id_penghuni)
                ->where("periode BETWEEN '$periode_a' AND '$periode_b'")
                ->get('pembayaran')
                ->row();
            if (!empty($data_pembayaran)) {
                $tgl_bayar = cek_tgl($data_pembayaran->tgl_bayar);
                $jml_bayar = $data_pembayaran->jml_bayar;
                $via = $data_pembayaran->via_pembayaran;
                $ket = $data_pembayaran->ket;
                $c_jml_bayar = $data_pembayaran->jml_bayar;
            } else {
                $tgl_bayar = '-';
                $jml_bayar = '-';
                $via = '-';
                $ket = '-';
                $c_jml_bayar = 0;
            }

            if ($d->status_penghuni == 1) {
                $fill = 'FFFF00';
                $c_real_pemasukan = $d->price;
            } else if ($d->status_penghuni == 2) {
                $fill = 'D8E4BC';
                $c_real_pemasukan = $d->price;
            } else if ($d->status_penghuni == 3) {
                $fill = 'cf795f';
                $c_real_pemasukan = 0;
            } else {
                $fill = 'ffffff';
                $c_real_pemasukan = 0;
            }
            $tgl_harusnya = cek_tgl($d->tgl_penempatan);

            $total_pemasukan_real += $c_jml_bayar;
            $total_pemasukan_seharusnya += $c_real_pemasukan;




            $styleBodyPendapatan = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => $fill]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000'],
                    ],
                ],
            ];
            //end data pembayaran

            //append to phpspreadsheet
            $excel->insertNewRowBefore($nr_pendapatan + 1, 1);
            $excel->setCellValue('A' . $start_pendapatan, "$no_pendapatan")
                ->setCellValue('B' . $start_pendapatan, " $tgl_harusnya ")
                ->setCellValue('C' . $start_pendapatan, " $tgl_bayar ")
                ->setCellValue('D' . $start_pendapatan, "$d->no_kamar")
                ->setCellValue('E' . $start_pendapatan, "$d->nama_penghuni")
                ->setCellValue('F' . $start_pendapatan, "$d->price")
                ->setCellValue('G' . $start_pendapatan, "$jml_bayar")
                ->setCellValue('H' . $start_pendapatan, "$via")
                ->setCellValue('I' . $start_pendapatan, "$ket");

            $excel->getStyle('A' . $start_pendapatan . ':i' . $start_pendapatan)->applyFromArray($styleBodyPendapatan);
            $excel->getStyle('A' . $start_pendapatan . ':i' . $start_pendapatan)->applyFromArray($styleBodyUniversal);
            //end append


            $start_pendapatan++;
            $nr_pendapatan++;
            $no_pendapatan++;
        }
        $st_tfoot_pendapatan = $start_pendapatan;
        $st_tfoot_pendapatan2 = $st_tfoot_pendapatan + 1;

        $selisih_pemasukan = $total_pemasukan_seharusnya - $total_pemasukan_real;

        $excel->mergeCells('A' . $st_tfoot_pendapatan . ':E' . $st_tfoot_pendapatan . '');
        $excel->mergeCells('H' . $st_tfoot_pendapatan . ':I' . $st_tfoot_pendapatan . '');

        $excel->mergeCells('A' . $st_tfoot_pendapatan2 . ':E' . $st_tfoot_pendapatan2 . '');
        $excel->mergeCells('F' . $st_tfoot_pendapatan2 . ':G' . $st_tfoot_pendapatan2 . '');
        $excel->mergeCells('H' . $st_tfoot_pendapatan2 . ':I' . $st_tfoot_pendapatan2 . '');

        $excel->getStyle('A' . $st_tfoot_pendapatan . ':I' . $st_tfoot_pendapatan2 . '')->applyFromArray($styleHeadPendapatan);


        $excel->setCellValue('A' . $st_tfoot_pendapatan, "Total")
            ->setCellValue('F' . $st_tfoot_pendapatan, "$total_pemasukan_seharusnya")
            ->setCellValue('G' . $st_tfoot_pendapatan, "$total_pemasukan_real")

            ->setCellValue('A' . $st_tfoot_pendapatan2, "Selisih")
            ->setCellValue('F' . $st_tfoot_pendapatan2, "$selisih_pemasukan");





        //end pendapatan










        //set heading pengeluaran
        $stylePengeluaran = [

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'name' => 'Arial',
                'bold' => true,
                'size' => 12,
                'underline' => true
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

        $st_pengeluaran = $st_tfoot_pendapatan2 + 3;

        $excel->getStyle('A' . $st_pengeluaran . ':E' . $st_pengeluaran . '')->applyFromArray($stylePengeluaran);
        $excel->mergeCells('A' . $st_pengeluaran . ':E' . $st_pengeluaran . '');
        $excel->setCellValue('A' . $st_pengeluaran, 'Pengeluaran');

        $st_thead_pengeluaran = $st_pengeluaran + 1;
        $excel->getStyle('A' . $st_thead_pengeluaran . ':E' . $st_thead_pengeluaran . '')->applyFromArray($styleHeadPengeluaran);
        $excel->setCellValue('A' . $st_thead_pengeluaran, '#')
            ->setCellValue('B' . $st_thead_pengeluaran, 'Tanggal')
            ->setCellValue('C' . $st_thead_pengeluaran, 'Biaya')
            ->setCellValue('D' . $st_thead_pengeluaran, 'Nominal')
            ->setCellValue('E' . $st_thead_pengeluaran, 'Ket');
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
        $start_pengeluaran =  $st_thead_pengeluaran + 1;
        $new_row_pengeluaran =  $st_thead_pengeluaran + 1;
        $total_pengeluaran = 0;
        foreach ($pengeluaran as $p) {
            $total_pengeluaran += $p->nominal;
            $tgl = cek_tgl($p->tanggal);

            $excel->insertNewRowBefore($new_row_pengeluaran + 1, 1);
            $excel->setCellValue('A' . $start_pengeluaran, "$no_pengeluaran")
                ->setCellValue('B' . $start_pengeluaran, "$tgl")
                ->setCellValue('C' . $start_pengeluaran, "$p->biaya")
                ->setCellValue('D' . $start_pengeluaran, "$p->nominal")
                ->setCellValue('E' . $start_pengeluaran, "$p->ket");
            // $excel->getStyle('L' . $start_pengeluaran . ':P' . $start_pengeluaran . '')->applyFromArray($styleBodyUniversal);
            $excel->getStyle('A' . $start_pengeluaran . ':E' . $start_pengeluaran . '')->applyFromArray($styleBodyPengeluaran);
            $no_pengeluaran++;
            $start_pengeluaran++;
            $new_row_pengeluaran++;
        }
        //end data pengeluaran


        $start_data_pengeluaran = $start_pengeluaran;
        $excel->mergeCells('A' . $start_data_pengeluaran . ':C' . $start_data_pengeluaran . '');
        $excel->mergeCells('D' . $start_data_pengeluaran . ':E' . $start_data_pengeluaran . '');
        $excel->getStyle('A' . $start_data_pengeluaran . ':E' . $start_data_pengeluaran . '')->applyFromArray($styleHeadPengeluaran);
        $excel->setCellValue('A' . $start_data_pengeluaran, 'Total');
        $excel->setCellValue('D' . $start_data_pengeluaran, "$total_pengeluaran");
        //end rekap data pengeluaran










        // //set heading setoran
        $st_setoran = $start_data_pengeluaran + 3;
        $st_headsetoran = $st_setoran + 1;
        $styleSetoran = [

            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],

            'font' => [
                'underline' => true,
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

        $excel->getStyle('A' . $st_setoran . ':D' . $st_setoran . '')->applyFromArray($styleSetoran);
        $excel->mergeCells('A' . $st_setoran . ':D' . $st_setoran . '');
        $excel->setCellValue('A' . $st_setoran, 'Setoran');

        $excel->getStyle('A' . $st_headsetoran . ':D' . $st_headsetoran . '')->applyFromArray($styleHeadSetoran);
        $excel->setCellValue('A' . $st_headsetoran, '#')
            ->setCellValue('B' . $st_headsetoran, 'Tanggal')
            ->setCellValue('C' . $st_headsetoran, 'Ket')
            ->setCellValue('D' . $st_headsetoran, 'Nominal');
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
        $start_setoran = $st_headsetoran + 1;
        $new_row_setoran = $st_headsetoran + 1;
        $total_setoran = 0;
        foreach ($setoran as $s) {
            $total_setoran += $s->nominal;
            $tgl = cek_tgl($s->tanggal);

            $excel->setCellValue('A' . $start_setoran, "$no_setoran")
                ->setCellValue('B' . $start_setoran, "$tgl")
                ->setCellValue('C' . $start_setoran, "$s->ket")
                ->setCellValue('D' . $start_setoran, "$s->nominal");
            $excel->getStyle('A' . $start_setoran . ':D' . $start_setoran . '')->applyFromArray($styleBodySetoran);
            $excel->getStyle('A' . $start_setoran . ':D' . $start_setoran . '')->applyFromArray($styleBodySetoran);

            $no_setoran++;
            $start_setoran++;
            $new_row_setoran++;
        }


        $tfoot_setoran = $start_setoran;

        $excel->mergeCells('A' . $tfoot_setoran . ':C' . $tfoot_setoran . '');
        $excel->setCellValue('A' . $tfoot_setoran, 'Total')
            ->setCellValue('D' . $tfoot_setoran, "$total_setoran");
        $excel->getStyle('A' . $tfoot_setoran . ':D' . $tfoot_setoran . '')->applyFromArray($styleHeadSetoran);

        //end body setoran

        $kost_name = $this->session->userdata('kost_name');
        $filename = $kost_name . ' ' . $show_periode . '.xlsx';



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //make it an attachment so we can define filename
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
