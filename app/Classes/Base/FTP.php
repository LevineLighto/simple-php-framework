<?php

namespace App\Classes\Base;

use Exception;

/**
 * Kelas Abstrak untuk FTP
 */
abstract class FTP extends Fetch {
    protected $path;
    protected $connection;
    protected bool $closable = true;

    public function __construct() {
        $credentials = static::fetch();

        $tries = 0;
        while(!$this->connection) {
            $this->connection = ftp_connect($credentials['host']);
            if($tries == 3) {
                break;
            }

            $tries++;
        }

        if($tries == 3 && !$this->connection) {
            $error = json_encode(error_get_last(), JSON_PRETTY_PRINT);
            throw new Exception("Tidak bisa tersambung ke ftp\n{$error}");
        }

        $login = ftp_login(
            $this->connection, 
            $credentials['username'], 
            $credentials['password']
        );

        if(!$login) {
            $error = json_encode(error_get_last(), JSON_PRETTY_PRINT);
            throw new Exception("Tidak bisa tersambung ke ftp\n{$error}");
        }

        ftp_pasv($this->connection, true);
        if(isset($this->path)) {
            $this->path = trim($this->path, '/');
            ftp_chdir($this->connection, "/{$this->path}/");
        }
    }

    public function FileExists(string $filename) {
        return $this->Size($filename) > 0;
    }

    public function Size(string $filename) {
        $size = ftp_size($this->connection, $filename);
        $this->Close();
        return $size;
    }

    public function Store($file, $name) {
        $response = ftp_put(
            $this->connection,
            $name,
            $file
        );
        $this->Close();
        return $response;
    }

    public function Replace(string $oldfile, $newfile, string $newname) {
        $this->closable = false;
        if($this->FileExists($oldfile)) {
            $this->Delete($oldfile);
        }

        $this->closable = true;
        $this->Store($newfile, $newname);
    }

    abstract public function Get(string $filename);

    public function Delete(string $filename) {
        if(!$this->FileExists($filename)) {
            return;
        }
        ftp_delete($this->connection, $filename);
        $this->Close();
    }

    public function Close() {
        if($this->closable) {
            ftp_close($this->connection);
        }
    }
}