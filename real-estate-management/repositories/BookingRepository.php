<?php
namespace RealEstateManagementApi\Repositories;

class BookingRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('bookings');
    }

    public function existsBookingNumber(string $booking_number, ?int $exclude_id = null): bool {
        return $this->exists('booking_number', $booking_number, $exclude_id);
    }
}
