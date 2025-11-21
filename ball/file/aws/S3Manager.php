<?php

namespace ball\file\aws;


use Aws\CloudFront\CloudFrontClient;
use Aws\S3\S3Client;
use Yii;

class S3Manager
{
    /**
     * @var S3Client
     */
    private $client;
    /**
     * @var CloudFrontClient
     */
    private $cfClient;
    /**
     * @var array
     */
    private $options;

    public function __construct()
    {
        $this->options = [
            'credentials' => [
                'key' => Yii::$app->params['aws']['id'],
                'secret' => Yii::$app->params['aws']['key'],
            ],
            'version' => Yii::$app->params['aws']['version'],
            'region' => Yii::$app->params['aws']['region'],
            'http' => [
                'verify' => false
            ]
        ];
        $this->client = new S3Client($this->options);
    }

    public function connect()
    {
        // no-op
    }

    /**
     * @param string $remote 遠端來源檔案路徑
     * @param string $local 目標檔案路徑
     * @return boolean 成功回傳true, 其他false
     */
    public function download(string $remote, string $local)
    {
        $bytes = file_put_contents($local, fopen(Domain::FS . $remote, 'r'));
        return $bytes > 0 ? true : false;
    }

    /**
     * @param string $local 來源檔案路徑
     * @param string $remote 目標檔案路徑
     * @param bool $invalidate
     * @return boolean 成功回傳true, 其他false
     */
    public function upload(string $local, string $remote, bool $invalidate = true)
    {
        $remote = ltrim($remote, "/");
        $object = new S3();
        $object->Body = file_get_contents($local);
        $object->Bucket = Yii::$app->params['aws']['bucket'];
        $object->Key = $remote;
        $ext = pathinfo($local, PATHINFO_EXTENSION);
        if ($ext == 'js') {
            $object->ContentType = 'text/javascript';
        } else if ($ext == 'css') {
            $object->ContentType = 'text/css';
        } else {
            $object->ContentType = \mime_content_type($local);
        }
        $this->client->putObject($object->attributes());
        if ($invalidate) {
            $this->invalidate($remote);
        }
        return true;
    }

    public function delete(string $remote, bool $invalidate = true)
    {
        $remote = ltrim($remote, '/');
        $object = new S3();
        $object->Bucket = Yii::$app->params['aws']['bucket'];
        $object->Key = $remote;
        try {
            $this->client->deleteObject($object->attributes());
            if ($invalidate) {
                $this->invalidate($remote);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteAll(string $remote, bool $invalidate = true)
    {
        $remote = ltrim($remote, '/');
        $bucket = Yii::$app->params['aws']['bucket'];
        try {
            $this->client->deleteMatchingObjects($bucket, $remote);
            if ($invalidate) {
                $this->invalidate($remote);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function copy (string $originName, string $newName, string $path)
    {
        $bucket = Yii::$app->params['aws']['bucket'];
        $path = ltrim($path, '/');

        try {
            $this->client->copyObject([
                'Bucket' => $bucket,
                'CopySource' => $bucket . '/' . $path . $originName,
                'Key' => $path . $newName,
                'MetadataDirective' => 'COPY'
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function rename(string $originName, string $newName, string $path)
    {
        if ($this->copy($originName, $newName, $path)) {
            if ($this->delete($path . $originName)) {
                return true;
            }
        }
        return false;
    }

    public function invalidate(string $remote)
    {
        $validateArray = [
            'DistributionId' => Yii::$app->params['aws']['distributionId'], // REQUIRED
            'InvalidationBatch' => [
                'CallerReference' => "/" . $remote . time(), // REQUIRED
                'Paths' => [ // REQUIRED
                    'Items' => ["/" . $remote],
                    'Quantity' => 1, // REQUIRED
                ]
            ]
        ];

        if (empty($this->cfClient)) {
            $this->cfClient = new CloudFrontClient($this->options);
        }
        $this->cfClient->createInvalidation($validateArray);
    }
}