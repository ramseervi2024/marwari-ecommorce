<?php
namespace CustomerManager\Models;

class Customer {
    public ?int $id = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public ?string $address = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $country = null;
    public ?string $postal_code = null;
    public string $status = 'ACTIVE';
    public int $is_deleted = 0;
    public string $created_at = '';
    public string $updated_at = '';

    /**
     * Customer constructor.
     */
    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->first_name = $data['first_name'] ?? '';
            $this->last_name = $data['last_name'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->phone = $data['phone'] ?? '';
            $this->address = $data['address'] ?? null;
            $this->city = $data['city'] ?? null;
            $this->state = $data['state'] ?? null;
            $this->country = $data['country'] ?? null;
            $this->postal_code = $data['postal_code'] ?? null;
            $this->status = $data['status'] ?? 'ACTIVE';
            $this->is_deleted = isset($data['is_deleted']) ? (int)$data['is_deleted'] : 0;
            $this->created_at = $data['created_at'] ?? '';
            $this->updated_at = $data['updated_at'] ?? '';
        }
    }

    /**
     * Serialize customer properties to array.
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
