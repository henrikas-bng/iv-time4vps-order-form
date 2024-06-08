<?php

require_once(realpath(dirname(__FILE__) . "/database.php"));

class Order {
    public int $id;
    public int $user_id;
    public int $order_number;
    public int $order_id;
    public int $invoice_id;
    public int $product_id;
    public string $service_type;
    public string $service_name;
    public float $total_price;

    /**
     * Create new order in database.
     */
    public function save() : bool {
        $q_fields = 'user_id,order_number,order_id,invoice_id,product_id,service_type,service_name,total_price';
        $q_values = '?,?,?,?,?,?,?,?';
        
        $query = Db::get()->prepare('INSERT INTO orders (' . $q_fields . ') VALUES (' . $q_values . ')');

        $response = $query->execute([
            $this->user_id,
            $this->order_number,
            $this->order_id,
            $this->invoice_id,
            $this->product_id,
            $this->service_type,
            $this->service_name,
            $this->total_price,
        ]);

        $this->id = Db::get()->lastInsertId();
        return $response;
    }
}
