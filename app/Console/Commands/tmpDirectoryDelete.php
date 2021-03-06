<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class tmpDirectoryDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmpDirectory:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 's3で画像を保存しているtmpディレクトリを削除します。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file_count = count(Storage::disk('s3')->files('/images/tmp'));
        $result = Storage::disk('s3')->deleteDirectory('/images/tmp');

        if ($result) {
            $this->info("tmpディレクトリの削除に成功しました。【削除ファイル数： ${file_count}】");
            logger()->info("tmpディレクトリの削除に成功しました。【削除ファイル数： ${file_count}】");
        }
        else {
            $this->error('tmpディレクトリの削除に失敗しました。');
            logger()->error('tmpディレクトリの削除に失敗しました。');
        }
    }
}
