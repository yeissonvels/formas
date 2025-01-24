<?php

/**
 * Class Order
 * Date: 15/11/2017
 * Time: 21:35:09
 */
class Order
{
    protected $id;
    protected $pdfid;
    protected $code;
    protected $customer;
    protected $telephone;
    protected $telephone2;
    protected $email;
    protected $deliveryzone;
    protected $deliverymonth;
    protected $store;
    protected $storename;
    protected $purchasedate; // Fecha de compra
    protected $deliveryrange; // Rangos de entrega 1-15 0 16-30
    protected $deliverydate; // Fecha de entrega
    protected $createdby;
    protected $createdon;
    protected $status;
    protected $total;
    protected $pendingpay;
    protected $pendingstatus;
    protected $paymethod;
    protected $paydate;
    protected $items = array();
    protected $comments = array();
    protected $incidences = array();
    protected $pdfparameters;
    protected $finishdeliveryfile;
    protected $finishdeliverycreatedon;
    protected $finishdeliverycreatedby;
    protected $cancelled;
    protected $pdfname;
    protected $totalitems;
    protected $saveditems;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPdfid()
    {
        return $this->pdfid;
    }

    /**
     * @param mixed $pdfid
     */
    public function setPdfid($pdfid)
    {
        $this->pdfid = $pdfid;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getTelephone2()
    {
        return $this->telephone2;
    }

    /**
     * @param mixed $telephone2
     */
    public function setTelephone2($telephone2)
    {
        $this->telephone2 = $telephone2;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getDeliveryzone()
    {
        return $this->deliveryzone;
    }

    /**
     * @param mixed $deliveryzone
     */
    public function setDeliveryzone($deliveryzone)
    {
        $this->deliveryzone = $deliveryzone;
    }

    /**
     * @return mixed
     */
    public function getDeliverymonth()
    {
        return $this->deliverymonth;
    }

    /**
     * @param mixed $deliverymonth
     */
    public function setDeliverymonth($deliverymonth)
    {
        $this->deliverymonth = $deliverymonth;
    }

    /**
     * @return mixed
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param mixed $store
     */
    public function setStore($store)
    {
        $this->store = $store;
    }

    /**
     * @return mixed
     */
    public function getStorename()
    {
        return $this->storename;
    }

    /**
     * @param mixed $storename
     */
    public function setStorename($storename)
    {
        $this->storename = $storename;
    }

    /**
     * @return mixed
     */
    public function getPurchasedate()
    {
        return $this->purchasedate;
    }

    /**
     * @param mixed $purchasedate
     */
    public function setPurchasedate($purchasedate)
    {
        $this->purchasedate = $purchasedate;
    }

    /**
     * @return mixed
     */
    public function getDeliveryrange()
    {
        return $this->deliveryrange;
    }

    /**
     * @param mixed $deliveryrange
     */
    public function setDeliveryrange($deliveryrange)
    {
        $this->deliveryrange = $deliveryrange;
    }

    /**
     * @return mixed
     */
    public function getDeliverydate()
    {
        return $this->deliverydate;
    }

    /**
     * @param mixed $deliverydate
     */
    public function setDeliverydate($deliverydate)
    {
        $this->deliverydate = $deliverydate;
    }

    /**
     * @return mixed
     */
    public function getCreatedby()
    {
        return $this->createdby;
    }

    /**
     * @param mixed $createdby
     */
    public function setCreatedby($createdby)
    {
        $this->createdby = $createdby;
    }

    /**
     * @return mixed
     */
    public function getCreatedon()
    {
        return $this->createdon;
    }

    /**
     * @param mixed $createdon
     */
    public function setCreatedon($createdon)
    {
        $this->createdon = $createdon;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getPendingpay()
    {
        return $this->pendingpay;
    }

    /**
     * @param mixed $pendingpay
     */
    public function setPendingpay($pendingpay)
    {
        $this->pendingpay = $pendingpay;
    }

    /**
     * @return mixed
     */
    public function getPendingstatus()
    {
        return $this->pendingstatus;
    }

    /**
     * @param mixed $pendingstatus
     */
    public function setPendingstatus($pendingstatus)
    {
        $this->pendingstatus = $pendingstatus;
    }

    /**
     * @return mixed
     */
    public function getPaymethod()
    {
        return $this->paymethod;
    }

    /**
     * @param mixed $paymethod
     */
    public function setPaymethod($paymethod)
    {
        $this->paymethod = $paymethod;
    }

    /**
     * @return mixed
     */
    public function getPaydate()
    {
        return $this->paydate;
    }

    /**
     * @param mixed $paydate
     */
    public function setPaydate($paydate)
    {
        $this->paydate = $paydate;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param array $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return array
     */
    public function getIncidences()
    {
        return $this->incidences;
    }

    /**
     * @param array $incidences
     */
    public function setIncidences($incidences)
    {
        $this->incidences = $incidences;
    }

    /**
     * @return mixed
     */
    public function getPdfparameters()
    {
        return $this->pdfparameters;
    }

    /**
     * @param mixed $pdfparameters
     */
    public function setPdfparameters($pdfparameters)
    {
        $this->pdfparameters = $pdfparameters;
    }

    /**
     * @return mixed
     */
    public function getFinishdeliveryfile()
    {
        return $this->finishdeliveryfile;
    }

    /**
     * @param mixed $finishdeliveryfile
     */
    public function setFinishdeliveryfile($finishdeliveryfile)
    {
        $this->finishdeliveryfile = $finishdeliveryfile;
    }

    /**
     * @return mixed
     */
    public function getFinishdeliverycreatedon()
    {
        return $this->finishdeliverycreatedon;
    }

    /**
     * @param mixed $finishdeliverycreatedon
     */
    public function setFinishdeliverycreatedon($finishdeliverycreatedon)
    {
        $this->finishdeliverycreatedon = $finishdeliverycreatedon;
    }

    /**
     * @return mixed
     */
    public function getFinishdeliverycreatedby()
    {
        return $this->finishdeliverycreatedby;
    }

    /**
     * @param mixed $finishdeliverycreatedby
     */
    public function setFinishdeliverycreatedby($finishdeliverycreatedby)
    {
        $this->finishdeliverycreatedby = $finishdeliverycreatedby;
    }

    /**
     * @return mixed
     */
    public function getCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @param mixed $cancelled
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
    }

    /**
     * @return mixed
     */
    public function getPdfname()
    {
        return $this->pdfname;
    }

    /**
     * @param mixed $pdfname
     */
    public function setPdfname($pdfname)
    {
        $this->pdfname = $pdfname;
    }

    /**
     * @return mixed
     */
    public function getTotalitems()
    {
        return $this->totalitems;
    }

    /**
     * @param mixed $totalitems
     */
    public function setTotalitems($totalitems)
    {
        $this->totalitems = $totalitems;
    }

    /**
     * @return mixed
     */
    public function getSaveditems()
    {
        return $this->saveditems;
    }

    /**
     * @param mixed $saveditems
     */
    public function setSaveditems($saveditems)
    {
        $this->saveditems = $saveditems;
    }
}