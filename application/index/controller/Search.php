<?php
namespace app\index\controller;

use app\index\model\Intention;
use app\index\model\Source;
use think\Controller;

class Search extends Controller
{

    public function customer()
    {
        $sources = Source::getSources();
        $this->assign("sources", $sources);

        $intentions = Intention::getIntentions();
        $this->assign("intentions", $intentions);
        return $this->fetch();
    }

    public function createIndex()
    {
        $redis = (new PhpRedisAdapter())->connect('127.0.0.1', 6379);

        $customerIndex = new Index($redis);
        $customerIndex->addTextField("realname")
            ->addNumericField("phone")
            ->addNumericField("cid")
            ->create();

        $documents = [];

        for($i=100;$i<10000;$i++) {
            $document = $customerIndex->makeDocument($i);
            $realname = 'liucunzhou'.$i;
            $document->realname->setValue($realname);
            $document->phone->setValue("11".$i);
            $document->cid->setValue(6739+$i);
            $documents[] = $document;

        }

        $customerIndex->addMany($documents, true);

    }

    public function addIndex(){
        $redis = (new PhpRedisAdapter())->connect('127.0.0.1', 6379);
        $customerIndex = new Index($redis);
        $customerIndex->setIndexName("member");
        $customerIndex->summarize(["realname","phone","cid"]);
        $documents = [];

        for($i=100;$i<10000;$i++) {
            $document = $customerIndex->makeDocument($i);
            $realname = 'liucunzhou'.$i;
            $document->realname->setValue($realname);
            $document->phone->setValue("11".$i);
            $document->cid->setValue(6739+$i);
            $documents[] = $document;

        }

        $customerIndex->addMany($documents);
    }

    public function search()
    {
        $Adapter = new PhpRedisAdapter();
        $Adapter->connect('127.0.0.1', 6379);

        $customerIndex = new Index($Adapter, "customer66");
        //$customerIndex->setIndexName("customer66");
        $customerIndex->limit(0, 10);
        $result = $customerIndex->search('liucunzhou', true);

        echo "<pre>";
        // print_r($result);
        $documents = $result->getDocuments();  // Number of documents.

        print_r($documents);
    }
}