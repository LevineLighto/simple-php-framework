<?php

namespace App\Classes;

use App\Classes\Base\FTP;
use App\Classes\Config\Error;
use App\Classes\Facade\DB;

class RemoteFile extends FTP{
    protected $path = 'uploadppdb';

    public function Get(string $filename){
        $this->closable = false;
        if(!$this->FileExists($filename)) {
            return Error::show(404);
        }

        $mime = File::Mime($filename);
        $size = $this->Size($filename);

        header("Content-type: {$mime}");
        header("Content-length: {$size}");
        header("Content-Disposition: inline; filename=\"{$filename}\"");
        header("Content-Transfer-Encoding: binary");

        ob_clean();
        flush();

        ftp_get($this->connection, "php://output", $filename);
        $this->closable = true;
        $this->Close();
        die();
    }

    public function Folder(string $name) {
        ftp_chdir($this->connection, $name);

        return $this;
    }

    // public function List() {
    //     $list = ftp_nlist($this->connection, '.');
    //     foreach($list as $index => $item) {
    //         $content    = explode('_', $item);
    //         $nosis      = $content[0];
    //         $type       = $content[1];
    //         array_splice($content, 0, 2);
    //         $name = $item;
    //         $list[$index] = compact('nosis', 'type', 'name');
    //     }
    //     return $list;
    // }

    // public function ListToDB() {
    //     $data = $this->List();

    //     foreach($data as $item) {
    //         if($item['type'] == 'kk') {
    //             $query = "SELECT id FROM tblFAyahBaru
    //                         WHERE nosis_ayah = ?
    //                         LIMIT 1";
    //             $ayah = DB::connection('siswa')
    //                     ->prepare($query, [$item['nosis'].'-1'])
    //                     ->first();

    //             if(!empty($ayah)) {
    //                 $id_ayah = $ayah->id;
    //             } else {
    //                 $id_ayah = null;
    //             }

    //             $query  = "SELECT id FROM tblFIbuBaru
    //                         WHERE nosis_ibu = ?
    //                         LIMIT 1";
    //             $ibu    = DB::connection('siswa')
    //                     ->prepare($query, [$item['nosis'].'-2'])
    //                     ->first();

    //             if(!empty($ibu)) {
    //                 $id_ibu = $ibu->id;
    //             } else {
    //                 $id_ibu = null;
    //             }

    //             DB::connection('siswa')
    //                 ->table('dokumen_kk')
    //                 ->insert([
    //                     'lokasi' => $item['nama'],
    //                 ]);
    //         }
    //     }
    // }
}