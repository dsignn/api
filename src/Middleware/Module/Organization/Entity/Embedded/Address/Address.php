<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity\Embedded\Address;

/**
 * Class Address
 * @package App\Module\Organization\Entity\Embedded\Address
 */
class Address {

    /**
     * @var string
     */
    protected $address;

        /**
     * @var string
     */
    protected $route;

    /**
     * @var string
     */
    protected $streetNumber;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var float
     */
    protected $lat;

    /**
     * @var float
     */
    protected $lng;

    /**
     * Get the value of address
     *
     * @return  string
     */ 
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @param  string  $address
     * @return  self
     */ 
    public function setAddress(string $address = null){
        $this->address = $address;
        return $this;
    }

    /**
     * Get the value of streetNumber
     *
     * @return  string
     */ 
    public function getStreetNumber() {
        return $this->streetNumber;
    }

    /**
     * Set the value of streetNumber
     *
     * @param  string  $streetNumber
     * @return  self
     */ 
    public function setStreetNumber(string $streetNumber = null) {
        $this->streetNumber = $streetNumber;
        return $this;
    }

    /**
     * Get the value of postalCode
     *
     * @return  string
     */ 
    public function getPostalCode() {
        return $this->postalCode;
    }

    /**
     * Set the value of postalCode
     *
     * @param  string  $postalCode
     * @return  self
     */ 
    public function setPostalCode(string $postalCode = null) {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Get the value of city
     * 
     * @return  string
     */ 
    public function getCity() {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @param  string  $city
     * @return  self
     */ 
    public function setCity(string $city = null) {
        $this->city = $city;
        return $this;
    }

    /**
     * Get the value of state
     *
     * @return  string
     */ 
    public function getState() {
        return $this->state;
    }

    /**
     * Set the value of state
     *
     * @param  string  $state
     * @return  self
     */ 
    public function setState(string $state = null)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get the value of lat
     *
     * @return  float
     */ 
    public function getLat() {
        return $this->lat;
    }

    /**
     * Set the value of lat
     *
     * @param  float  $lat
     * @return  self
     */ 
    public function setLat(float $lat = null) {
        $this->lat = $lat;
        return $this;
    }

    /**
     * Get the value of lng
     *
     * @return  float
     */ 
    public function getLng() {
        return $this->lng;
    }

    /**
     * Set the value of lng
     *
     * @param  float  $lng
     * @return  self
     */ 
    public function setLng(float $lng = null) {
        $this->lng = $lng;
        return $this;
    }

    /**
     * Get the value of route
     *
     * @return  string
     */ 
    public function getRoute() {
        return $this->route;
    }

    /**
     * Set the value of route
     *
     * @param  string  $route
     * @return  self
     */ 
    public function setRoute(string $route = null) {
        $this->route = $route;
        return $this;
    }

    /**
     * Get the value of county
     *
     * @return  string
     */ 
    public function getCountry() {
        return $this->country;
    }

    /**
     * Set the value of county
     *
     * @param  string  $county
     * @return  self
     */ 
    public function setCountry(string $country = null) {
        $this->country = $country;
        return $this;
    }
}