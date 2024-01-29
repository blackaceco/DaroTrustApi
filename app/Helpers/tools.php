<?php

use Illuminate\Support\Facades\Storage;

function CarbonPrinter($date, $type = "humans", $strVal = 'n/a')
{
    if (!is_null($date)) {
        $date = Carbon\Carbon::parse($date);

        if (!is_null($date)) {
            switch ($type) {
                case 'humans':
                    $strVal = $date->diffForHumans();
                    break;

                case 'datetime':
                    $strVal = $date->toDateTimeString();
                    break;

                case 'date':
                    $strVal = $date->format('Y-m-d');
                    break;

                case 'time':
                    $strVal = $date->format('h:i a');
                    break;

                case 'day_month':
                    $strVal = $date->format('d M');
                    break;
            }
        }
    }


    return $strVal;
}


/**
 * Generating a url for the front especially to uploading files to the S3's storage
 * and the link will available for specific period then it will expired
 *
 * @param String $file
 * @param Integer $validSeconds = 60
 *
 * @return String
 */
function generateSignedUrlFromS3($file, $validSeconds = 60, $command = "PutObject")
{
    $client = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => config('filesystems.disks.s3.region'),
        'credentials' => [
            'key'    => config('filesystems.disks.s3.key'),
            'secret' => config('filesystems.disks.s3.secret')
        ],
    ]);

    $command = $client->getCommand($command, [
        'Bucket' => config('filesystems.disks.s3.bucket'),
        'Key' => $file
    ]);

    $result = $client->createPresignedRequest($command, now()->addSeconds($validSeconds));

    return (string) $result->getUri();
}


function getFileLink($file)
{
    if (is_null($file))
        return null;

        
    if (config('filesystems.default') == 'local')
        $file = asset("storage/$file");
    elseif (config('filesystems.default') == 's3')
        $file = Storage::disk('s3')->url($file);   // generateSignedUrlFromS3($file, 600, "GetObject");
    else
        return null;


    // response
    return urldecode($file);
}
