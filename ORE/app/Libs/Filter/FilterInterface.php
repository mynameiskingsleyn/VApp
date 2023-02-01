<?php
namespace App\Libs\Filter;

interface FilterInterface {
    public function geoLocation(array $request);
    public function getAllDealer();
    public function getAllVehicle();
    public function create(array $data);
    public function delete($id);
    public function getByID($id);
    public function update($id,array $data);
}