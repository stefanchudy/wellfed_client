<?php

class model_donations extends System\Model
{

    /**
     *
     * @var \Utility\Connector $connector
     */
    private $_connector = null;

    private $_donations = NULL;

    /**
     *
     * @var \Utility\OrmDataset $_locations
     */
    private $_locations = NULL;

    /**
     *
     * @var \Utility\OrmDataset $_foodTypes
     */
    private $_foodTypes = NULL;

    /**
     *
     * @var \Utility\OrmDataset $_booked
     */
    private $_booked = NULL;

    private $_collection = null;
    private $_booking_expiring = 3;

    public function init()
    {
        $this->_connector = new \Utility\Connector;
    }

    public function getCollection($refresh = false)
    {
        if ($refresh) {
            $this->_collection = null;
        }
        if ($this->_collection === null) {
            $this->_collection = $this->_connector->donations_getcollection();
        }
        return $this->_collection;
    }

    public function getDashboardTable()
    {
        $this->_cleanBookings();
        $expired = date('Y-m-d H:i:s', strtotime('now - ' . $this->_booking_expiring . ' hours'));
        $result = Array();
        $result_unsorted = Array();
        $sort_map = Array();
        foreach ($this->getCollection() as $k => $v) {
            if ((strtotime($v['date_expire']) > strtotime($expired)) && $v['quantity_remain']) {
                $remain = strtotime($v['date_expire']) - strtotime($expired);
                $sort_map[$k] = $remain;
                $result_unsorted[$k] = Array(
                    'id' => $k,
                    'timer' => strtotime($v['date_expire']),
                    'title' => $v['title'],
                    'location' => $v['location_data']['location_title'],
                    'location_id' => $v['location_data']['id'],
                    'food_type' => $v['computed']['food_type_path'],
                    'status' => $v['computed']['status_html'],
                    'qty' => $v['quantity'],
                    'qty_booked' => $v['quantity_booked'],
                    'qty_remain' => $v['quantity_remain'],
                );
            }
        }

        asort($sort_map);

        foreach (array_keys($sort_map) as $sort_key) {
            $result[$sort_key] = $result_unsorted[$sort_key];
        }

        return $result;
    }

    //private
    private function _cleanBookings()
    {
        $expired = date('Y-m-d H:i:s', strtotime('now - ' . $this->_booking_expiring . ' hours'));
        // $query = 'DELETE FROM `booked_donations` WHERE `date_booked` < "' . $expired . '" AND `delivered` = 0';
        // $this->db->query($query);
    }

    public function getActiveDonationsByLocation($location_id)
    {
        $_collection = $this->getCollection();
        $result = Array();
        foreach ($_collection as $item_id => $collection_item) {
            if (($collection_item['location_id'] == $location_id) && ($collection_item['computed']['status_bool']) && ($collection_item['quantity_remain'] > 0)) {
                $result[$item_id] = $collection_item;
            }
        }
        return $result;
    }

    public function getDonation($id)
    {
        $collection = $this->getCollection();
        return isset($collection[$id]) ? $collection[$id] : null;
    }

    public function addDonation($params)
    {
        $this->_collection = null;
        return $this->_connector->donations_add($params);
    }

    public function updateDonation($id, $data)
    {
        $this->_collection = null;
        return $this->_connector->donations_update(array(
            'id' => $id,
            'data' => $data
        ));
    }

    public function resetDonation($id, $time)
    {
        $this->_collection = null;
        return $this->_connector->donations_reset(array(
            'id' => $id,
            'time' => $time
        ));
    }

    public function bookDonation($user_id, $donation_id, $quantity, $phone = null, $delivery = 0, $address = '')
    {
        $this->_collection = null;
        return $this->_connector->donations_book(array(
            'user_id' => $user_id,
            'donation_id' => $donation_id,
            'quantity' => $quantity,
            'phone' => $phone,
            'delivery' => $delivery,
            'address' => $address
        ));
    }

    public function deleteBooking($id)
    {
        $this->_collection = null;
        return $this->_connector->donations_unbook(array('id' => $id));
    }

    public function getBooked($filter = Array())
    {
        $result = Array(
            'total' => 0,
            'entries' => Array()
        );
        $donations = $this->getCollection();

        foreach ($donations as $donation_id => $donation) {
            if (isset($filter['donation_id']) && ($filter['donation_id'] != $donation_id))
                continue;
            foreach ($donation['booking'] as $booking) {
                if (isset($filter['user_id']) && ($filter['user_id']) != $booking['user_id'])
                    continue;
                $result['total'] += $booking['quantity'];
                $result['entries'][$booking['id']] = $booking;
//                $result['entries'][booking['id']]['date_expire'] = $booked;
            }
        }


        return $result;
    }

    public function delete($id)
    {
        $this->_collection = null;
        return $this->_connector->donations_delete(array('id' => $id));
    }

    public function getCityList()
    {
        return $this->_connector->donations_citylist();
    }

    public function searchForDonation($lat, $lng, $allergens = Array(), $preferences = 0, $booking_radius = null, $booking_results_limit = null)
    {
        if ($booking_radius === NULL) {
            $booking_radius = $this->db_settings->get('booking_radius', 30);
        }

        if ($booking_results_limit === NULL) {
            $booking_results_limit = $this->db_settings->get('booking_results_limit', 10);
        }

        return $this->_connector->donations_search(
            array(
                'lat' => $lat,
                'lng' => $lng,
                'allergens' => $allergens,
                'preferences' => $preferences,
                'radius' => $booking_radius,
                'limit' => $booking_results_limit
            )
        );
    }

    public function deliverBooking($id)
    {
        return $this->_connector->booking_taken(
            array(
                'id' => $id
            )
        );
    }

    public function resetBooking($id)
    {
        return $this->_connector->booking_reset(
            array(
                'id' => $id
            )
        );
    }

}