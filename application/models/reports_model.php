<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * Reports class to make database queries that create reports tools
 * 
 * @author pyasi
 *
 */

class Reports_Model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }	


    function services_by_org($organization_id)
    {
        // services by organization
        $query = "SELECT u.id u_id, u.account_number u_ac, ".
            "u.master_service_id u_msid, u.billing_id u_bid, ".
            "u.removed u_rem, u.usage_multiple u_usage, ".
            "b.next_billing_date b_next_billing_date, b.id b_id, ".
            "b.billing_type b_type, t.id t_id, t.frequency t_freq, ".
            "t.method t_method, m.service_description m_service_description, ".
            "m.id m_id, m.pricerate m_pricerate, m.frequency m_freq ".
            "FROM user_services u ".
            "LEFT JOIN master_services m ON u.master_service_id = m.id ".
            "LEFT JOIN billing b ON u.billing_id = b.id ".
            "LEFT JOIN billing_types t ON b.billing_type = t.id ".
            "WHERE b.organization_id = ? ".
            "AND t.method <> 'free' AND u.removed <> 'y'";

        $result = $this->db->query($query, array($organization_id))
            or die ("services_by_org Query Failed");

        return $result->result_array();

    }


    function taxes_by_org($organization_id)
    {
        $query = "SELECT ts.id ts_id, ts.master_services_id ts_serviceid, ".
            "ts.tax_rate_id ts_rateid, ms.id ms_id, ".
            "ms.service_description ms_description, ms.pricerate ms_pricerate, ".
            "ms.frequency ms_freq, tr.id tr_id, tr.description tr_description, ".
            "tr.rate tr_rate, tr.if_field tr_if_field, tr.if_value tr_if_value, ".
            "tr.percentage_or_fixed tr_percentage_or_fixed, ".
            "us.master_service_id us_msid, us.billing_id us_bid, us.id us_id, ".
            "us.removed us_removed, us.account_number us_account_number, ". 
            "us.usage_multiple us_usage_multiple,  ".
            "te.account_number te_account_number, te.tax_rate_id te_tax_rate_id, ".
            "b.id b_id, b.billing_type b_billing_type, ".
            "t.id t_id, t.frequency t_freq, t.method t_method ".
            "FROM taxed_services ts ".
            "LEFT JOIN user_services us ON ".
            "us.master_service_id = ts.master_services_id ".
            "LEFT JOIN master_services ms ON ms.id = ts.master_services_id ".
            "LEFT JOIN tax_rates tr ON tr.id = ts.tax_rate_id ".
            "LEFT JOIN tax_exempt te ON te.account_number = us.account_number ".
            "AND te.tax_rate_id = tr.id ".
            "LEFT JOIN billing b ON us.billing_id = b.id ".
            "LEFT JOIN billing_types t ON b.billing_type = t.id ".
            "WHERE b.organization_id = ? ".
            "AND us.removed <> 'y'";

        $taxresult = $this->db->query($query, array($organization_id))
            or die ("taxes_by_org Query Failed");

        return $taxresult->result_array;
    }


    /*
     * ------------------------------------------------------------------------
     *  get master services description, price, category, and frequency by id
     * ------------------------------------------------------------------------
     */
    function master_service_info($id)
    {
        $query = "SELECT ms.service_description, ms.pricerate, ms.category, ".
            "ms.frequency FROM master_services ms ".
            "WHERE ms.id = ?";
        $serviceresult = $this->db->query($query, array($id))
            or die ("master_services_info Query Failed");

        return $serviceresult->result_array();
    }


    function taxed_services($id)
    {
        $query = "SELECT tr.description, tr.rate, ms.service_description, ".
            "ms.category FROM tax_rates tr ".
            "LEFT JOIN taxed_services ts ON ts.tax_rate_id = tr.id ".
            "LEFT JOIN master_services ms ON ms.id = ts.master_services_id ".	
            "WHERE ts.id = ?";
        $taxresult = $this->db->query($query, array($id))
            or die ("taxed_services Query Failed");

        return $taxresult->result_array();
    }


    function total_services($organization_id)
    {
        // get the total services for each billing type
        $query = "SELECT m.id m_id, m.service_description m_servicedescription, ".
            "m.pricerate m_pricerate, m.frequency m_frequency, ".
            "m.organization_id m_organization_id, g.org_name g_org_name, ".
            "u.removed u_removed, u.master_service_id u_msid, ".
            "count(bt.method) AS TotalNumber, ".
            "b.id b_id, b.billing_type b_billing_type, bt.id bt_id, ".
            "bt.method bt_method ". 
            "FROM user_services u ".
            "LEFT JOIN master_services m ON u.master_service_id = m.id ".
            "LEFT JOIN billing b ON b.id = u.billing_id ".
            "LEFT JOIN billing_types bt ON b.billing_type = bt.id ".
            "LEFT JOIN general g ON m.organization_id = g.id ".
            "WHERE u.removed <> 'y' AND bt.method <> 'free' ".
            "AND b.organization_id = ? AND m.pricerate > '0' ". 
            "AND m.frequency > '0' GROUP BY bt.method ORDER BY TotalNumber";
        $result = $this->db->query($query, array($organization_id))
            or die ("total_services query failed");

        return $result->result_array();

    }


    function services_in_categories($organization_id)
    {
        // get the number of services in each category
        $query = "SELECT m.id m_id, m.service_description m_servicedescription, ".
            "m.pricerate m_pricerate, m.category m_category, m.frequency m_frequency, ".
            "m.organization_id m_organization_id, g.org_name g_org_name, ".
            "u.removed u_removed, u.master_service_id u_msid, ".
            "count(bt.method) AS TotalNumber, ".
            "b.id b_id, b.billing_type b_billing_type, bt.id bt_id, bt.method bt_method ".
            "FROM user_services u ".
            "LEFT JOIN master_services m ON u.master_service_id = m.id ".
            "LEFT JOIN billing b ON b.id = u.billing_id ".
            "LEFT JOIN billing_types bt ON b.billing_type = bt.id ".
            "LEFT JOIN general g ON m.organization_id = g.id ".
            "WHERE u.removed <> 'y' AND bt.method <> 'free' AND b.organization_id = ? ".
            "AND m.frequency > '0' GROUP BY m.category ORDER BY TotalNumber DESC";
        $result = $this->db->query($query, array($organization_id))
            or die ("services_in_categories query failed");

        return $result->result_array();

    }


    function number_of_customers()
    {
        // get the number of customers
        $query = "SELECT COUNT(*) FROM customer WHERE cancel_date is NULL";
        $result = $this->db->query($query) or die ("number_of_customers query failed");
        $myresult = $result->row_array();

        return $myresult['COUNT(*)'];

    }


    function number_of_non_free_customers()
    {
        // get the number of customers who are not free
        $query = "SELECT COUNT(*) FROM customer c
            LEFT JOIN billing b ON b.id = c.default_billing_id 
            LEFT JOIN billing_types bt ON b.billing_type = bt.id
            WHERE cancel_date is NULL AND bt.method <> 'free'";
        $result = $this->db->query($query) or die ("number_of_non_free_customers query failed");
        $myresult = $result->row_array();

        return $myresult['COUNT(*)'];
    }


    function servicerevenue($day1, $day2, $org_id)
    {
        // show payments for a specified date range according to 
        // their service category
        if ($org_id == 'all')
        {
            $query = "SELECT ROUND(SUM(bd.paid_amount),2) AS CategoryTotal, 
                ROUND(SUM(bd.billed_amount),2) AS CategoryBilled,
                COUNT(DISTINCT us.id) As ServiceCount, 
                ms.category service_category, 
                ms.service_description, service_description,  
                g.org_name g_org_name 
                FROM billing_details bd 
                LEFT JOIN user_services us ON us.id = bd.user_services_id 
                LEFT JOIN master_services ms ON us.master_service_id = ms.id 
                LEFT JOIN general g ON ms.organization_id = g.id 
                WHERE bd.creation_date BETWEEN ? AND ? 
                AND bd.taxed_services_id IS NULL 
                GROUP BY ms.id ORDER BY ms.category";

            $result = $this->db->query($query, array($day1, $day2))
                or die ("servicerevenue query failed");
        }
        else
        {
            $query = "SELECT ROUND(SUM(bd.paid_amount),2) AS CategoryTotal, 
                ROUND(SUM(bd.billed_amount),2) AS CategoryBilled,
                COUNT(DISTINCT us.id) As ServiceCount, 
                ms.category service_category, 
                ms.service_description, service_description,  
                g.org_name g_org_name 
                FROM billing_details bd 
                LEFT JOIN user_services us ON us.id = bd.user_services_id 
                LEFT JOIN master_services ms ON us.master_service_id = ms.id 
                LEFT JOIN general g ON ms.organization_id = g.id 
                WHERE bd.creation_date BETWEEN ? AND ? 
                AND bd.taxed_services_id IS NULL AND g.id = ? 
                GROUP BY ms.id ORDER BY ms.category";

            $result = $this->db->query($query, array($day1, $day2, $org_id))
                or die ("servicerevenue 2 query failed");
        }


        return $result->result_array();

    }


    function creditrevenue($day1, $day2, $org_id)
    {
        // show credits for a specified date range according to 
        // their credit_options description
        if ($org_id == 'all')
        {
            $query = "SELECT ROUND(SUM(bd.paid_amount),2) AS CategoryTotal, 
                ROUND(SUM(bd.billed_amount),2) AS CategoryBilled,
                COUNT(DISTINCT us.id) As ServiceCount, 
                cr.description credit_description, 
                g.org_name g_org_name 
                FROM billing_details bd
                LEFT JOIN user_services us ON us.id = bd.user_services_id 
                LEFT JOIN master_services ms ON us.master_service_id = ms.id 
                LEFT JOIN credit_options cr ON cr.user_services = us.id
                LEFT JOIN general g ON g.id = ms.organization_id 
                WHERE bd.creation_date BETWEEN ? AND ? 
                AND bd.taxed_services_id IS NULL 
                AND ms.id = 1  
                GROUP BY cr.description"; 

            $result = $this->db->query($query, array($day1, $day2))
                or die ("creditrevenue query failed");
        }
        else
        {
            $query = "SELECT ROUND(SUM(bd.paid_amount),2) AS CategoryTotal, 
                ROUND(SUM(bd.billed_amount),2) AS CategoryBilled,
                COUNT(DISTINCT us.id) As ServiceCount, 
                cr.description credit_description, 
                g.org_name g_org_name 
                FROM billing_details bd
                LEFT JOIN user_services us ON us.id = bd.user_services_id 
                LEFT JOIN master_services ms ON us.master_service_id = ms.id 
                LEFT JOIN credit_options cr ON cr.user_services = us.id
                LEFT JOIN general g ON g.id = ms.organization_id 
                WHERE bd.creation_date BETWEEN ? AND ? 
                AND bd.taxed_services_id IS NULL AND g.id = ? 
                AND ms.id = 1  
                GROUP BY cr.description"; 

            $result = $this->db->query($query, array($day1, $day2, $org_id))
                or die ("creditrevenue 2 query failed");
        }

        return $result->result_array();

    }


    function refundrevenue($day1, $day2, $org_id)
    {
        // show service refunds for a specified date range according to 
        // their refund_date
        if ($org_id == 'all')
        {
            $query = "SELECT ROUND(SUM(bd.refund_amount),2) AS CategoryTotal,
                COUNT(DISTINCT us.id) As ServiceCount,  
                ms.category service_category, 
                ms.service_description service_description, 
                g.org_name g_org_name    
                FROM billing_details bd
                LEFT JOIN user_services us 
                ON us.id = bd.user_services_id 
                LEFT JOIN master_services ms 
                ON us.master_service_id = ms.id
                LEFT JOIN general g 
                ON g.id = ms.organization_id  
                WHERE bd.refund_date BETWEEN ? AND ? 
                AND bd.taxed_services_id IS NULL 
                GROUP BY ms.id"; 

            $result = $this->db->query($query, array($day1, $day2))
                or die ("refundrevenue query failed");
        }
        else
        {
            $query = "SELECT ROUND(SUM(bd.refund_amount),2) AS CategoryTotal,
                COUNT(DISTINCT us.id) As ServiceCount,  
                ms.category service_category, 
                ms.service_description service_description, 
                g.org_name g_org_name    
                FROM billing_details bd
                LEFT JOIN user_services us 
                ON us.id = bd.user_services_id 
                LEFT JOIN master_services ms 
                ON us.master_service_id = ms.id
                LEFT JOIN general g 
                ON g.id = ms.organization_id  
                WHERE bd.refund_date BETWEEN ? AND ? 
                AND bd.taxed_services_id IS NULL and g.id = ? 
                GROUP BY ms.id"; 

            $result = $this->db->query($query, array($day1, $day2, $org_id))
                or die ("refunerevenue 2 query failed");
        }

        return $result->result_array();

    }


    function discountrevenue($day1, $day2, $org_id)
    {
        // show discounts entered for a specified date range
        if ($org_id == 'all')
        {
            $query = "SELECT ph.billing_amount, ph.invoice_number, ".
                "ph.creation_date, bi.name, bi.company ".
                "FROM payment_history ph ".
                "LEFT JOIN billing bi ON ph.billing_id = bi.id ".
                "WHERE ph.creation_date BETWEEN ? AND ? ".
                "AND ph.payment_type = 'discount'";

            $result = $this->db->query($query, array($day1, $day2))
                or die ("discountrevenue query failed");
        }
        else
        {
            // show discounts entered for a specified date range
            $query = "SELECT ph.billing_amount, ph.invoice_number, ".
                "ph.creation_date, bi.name, bi.company ".
                "FROM payment_history ph ".
                "LEFT JOIN billing bi ON ph.billing_id = bi.id ".
                "WHERE ph.creation_date BETWEEN ? AND ? ".
                "AND ph.payment_type = 'discount' AND bi.organization_id = ?";

            $result = $this->db->query($query, array($day1, $day2, $org_id))
                or die ("discountrevenue 2 query failed");

        }

        return $result->result_array();
    }



    function taxrevenue($day1, $day2)
    {
        // show taxes for a specified date range according to
        // their tax rate description
        $query = "SELECT ROUND(SUM(bd.paid_amount),2)
            AS CategoryTotal,
            ROUND(SUM(bd.billed_amount),2) AS CategoryBilled,
            COUNT(DISTINCT bd.id) As ServiceCount,
            tr.description tax_description
            FROM billing_details bd
            LEFT JOIN taxed_services ts
            ON bd.taxed_services_id = ts.id
            LEFT JOIN tax_rates tr ON ts.tax_rate_id = tr.id
            WHERE bd.creation_date BETWEEN ? AND ? 
            AND bd.taxed_services_id IS NOT NULL
            GROUP BY tr.id";

        $result = $this->db->query($query, array($day1, $day2))
            or die ("taxrevenue query failed");

        return $result->result_array();

    }


    function taxrefunds($day1, $day2)
    {
        // show tax refunds for a specified date range according to 
        // their tax rate description
        $query = "SELECT ROUND(SUM(bd.refund_amount),2) 
            AS CategoryTotal,
            COUNT(DISTINCT bd.id) As ServiceCount,  
            tr.description tax_description  
            FROM billing_details bd 
            LEFT JOIN taxed_services ts 
            ON bd.taxed_services_id = ts.id 
            LEFT JOIN tax_rates tr ON ts.tax_rate_id = tr.id 
            WHERE bd.refund_date BETWEEN ? AND ? 
            AND bd.taxed_services_id IS NOT NULL 
            GROUP BY tr.id";

        $result = $this->db->query($query, array($day1, $day2))
            or die ("taxrefunds query failed");

        return $result->result_array();

    }


    function refunds($organization_id, $day1, $day2)
    {
        $query = "SELECT ROUND(SUM(bd.refund_amount), 2) AS refund_amount, bd.refund_date, us.account_number, cu.name, 
            ms.category service_category, ms.service_description service_description, bd.invoice_number, ph.creditcard_number
            FROM billing_details bd
            LEFT JOIN user_services us ON us.id = bd.user_services_id
            LEFT JOIN master_services ms ON us.master_service_id = ms.id
            LEFT JOIN customer cu ON us.account_number = cu.account_number
            LEFT JOIN payment_history ph ON bd.payment_history_id = ph.id 
            WHERE bd.refund_date
            BETWEEN '$day1' AND '$day2'"; 
        $result = $this->db->query($query, array($organization_id, $day1, $day2))
            or die ("refunds query failed");

        return $result->result_array();
    }


    function recentpayments($organization_id, $viewstatus)
    {
        // get the most recent payment history id for each billing id in that org
        $query = "SELECT max(ph.id) my_id, ph.billing_id my_bid ".
            "FROM payment_history ph ".
            "LEFT JOIN billing b ON b.id = ph.billing_id ".
            "WHERE b.organization_id = ? ".
            "GROUP BY ph.billing_id ORDER BY ph.billing_id";

        $result = $this->db->query($query, array($organization_id))
            or die ("recentpayments query failed");

        // initialize for multidimensional result array
        $i = 0;
        $payments = array();
        $duedates = array();

        // go through each one and find one with status we want to show
        foreach ($result->result_array() AS $myresult) 
        {
            $recentpaymentid = $myresult['my_id'];

            if (($viewstatus == 'authorized') OR ($viewstatus == 'declined') 
                OR ($viewstatus == 'pending') OR ($viewstatus == 'turnedoff') 
                OR ($viewstatus == 'pastdue') OR ($viewstatus == 'noticesent')
                OR ($viewstatus == 'waiting')) 
            {
                // don't include past due exempts in this listing
                $query = "SELECT ph.billing_id, b.account_number, b.name, b.company, ".
                    "ph.status, bd.invoice_number, bh.payment_due_date, bh.from_date, bh.to_date, c.cancel_date ".
                    "FROM payment_history ph ".
                    "LEFT JOIN billing b ON b.id = ph.billing_id ".
                    "LEFT JOIN billing_details bd ON bd.billing_id = b.id ".
                    "LEFT JOIN billing_history bh ON bd.invoice_number = bh.id ".
                    "LEFT JOIN customer c ON c.account_number = b.account_number ".
                    "WHERE ph.id = ? AND b.pastdue_exempt <> 'y' AND ".
                    "c.cancel_date IS NULL AND ".
                    "ph.status = ? AND bd.billed_amount > bd.paid_amount LIMIT 1";

                $paymentresult = $this->db->query($query, array($recentpaymentid, $viewstatus))
                    or die ("paymentresult queryfailed");

            } 
            elseif (($viewstatus == 'cancelwfee') OR ($viewstatus == 'canceled') 
                OR ($viewstatus == 'collections')) 
            {
                // ok to include pastdue exempt accounts in this listing
                $query = "SELECT ph.billing_id, b.account_number, b.name, b.company, ".
                    "ph.status, bd.invoice_number, bh.payment_due_date, bh.from_date, bh.to_date, c.cancel_date ".
                    "FROM payment_history ph ".
                    "LEFT JOIN billing b ON b.id = ph.billing_id ".
                    "LEFT JOIN billing_details bd ON bd.billing_id = b.id ".
                    "LEFT JOIN billing_history bh ON bd.invoice_number = bh.id ".
                    "LEFT JOIN customer c ON c.account_number = b.account_number ".
                    "WHERE ph.id = ? AND ".
                    "ph.status = ? AND bd.billed_amount > bd.paid_amount LIMIT 1";

                $paymentresult = $this->db->query($query, array($recentpaymentid, $viewstatus))
                    or die ("paymentresult queryfailed");

            } 
            elseif ($viewstatus == 'pastdueexempt') 
            {
                $query = "SELECT ph.billing_id, b.account_number, b.name, b.company, ".
                    "ph.status, bd.invoice_number, bh.payment_due_date, bh.from_date, bh.to_date, c.cancel_date ".
                    "FROM payment_history ph ".
                    "LEFT JOIN billing b ON b.id = ph.billing_id ".
                    "LEFT JOIN billing_details bd ON bd.billing_id = b.id ".
                    "LEFT JOIN billing_history bh ON bd.invoice_number = bh.id ".
                    "LEFT JOIN customer c ON c.account_number = b.account_number ".
                    "WHERE ph.id = ? AND b.pastdue_exempt = 'y' ".
                    "AND c.cancel_date IS NULL AND bd.billed_amount > bd.paid_amount LIMIT 1";

                $paymentresult = $this->db->query($query, array($recentpaymentid))
                    or die ("paymentresult queryfailed");

            }

            foreach ($paymentresult->result_array() AS $mypaymentresult) 
            {    
                $account_number = $mypaymentresult['account_number'];
                $billing_id = $mypaymentresult['billing_id'];    
                $name = $mypaymentresult['name'];
                $company = $mypaymentresult['company'];
                $status = $mypaymentresult['status'];
                $invoice_number = $mypaymentresult['invoice_number'];
                $from_date = $mypaymentresult['from_date'];
                $to_date = $mypaymentresult['to_date'];
                $payment_due_date = $mypaymentresult['payment_due_date'];

                // TODO: select unique categories of service for this billing id from
                $categorylist = "";
                $query = "SELECT DISTINCT m.category FROM user_services u ".
                    "LEFT JOIN master_services m ON u.master_service_id = m.id ".
                    "WHERE u.billing_id = ? AND removed <> 'y'";
                $categoryresult = $this->db->query($query, array($billing_id))
                    or die ("distince category query failed");
                foreach ($categoryresult->result_array() AS $mycategoryresult) 
                {
                    $categorylist .= $mycategoryresult['category'];
                    $categorylist .= "<br>";
                }

                $pastcharges = sprintf("%.2f",$this->billing_model->total_pastdueitems($billing_id));

                // put the data in an array to return
                $payments[$i] = array(
                    'account_number' => $account_number,
                    'billing_id' => $billing_id,
                    'name' => $name,
                    'company' => $company,
                    'status' => $status,
                    'invoice_number' => $invoice_number,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'payment_due_date' => $payment_due_date,
                    'categorylist' => $categorylist,
                    'pastcharges' => $pastcharges
                );

                $duedates[$i] = $payment_due_date;

                $i++;

            }

        }

        // sort the payments according to due date
        array_multisort($duedates, SORT_ASC, $payments);

        return $payments;

    }


    function paymentstatus($day1, $day2, $organization_id, $showpaymenttype)
    {
        // get the organization info
        // special id = all queries all organizations combined
        if ($organization_id == "all") 
        {
            $query = "SELECT p.*, b.name, b.phone FROM payment_history p ".
                "LEFT JOIN billing b ON p.billing_id = b.id WHERE p.creation_date ".
                "BETWEEN ? AND ? AND p.payment_type = ?";

            $result = $this->db->query($query, array($day1, $day2, $showpaymenttype))
                or die ("paymentstatus 1 queryfailed");
        } 
        else 
        {
            $query = "SELECT p.*, b.name, b.phone FROM payment_history p ".
                "LEFT JOIN billing b ON p.billing_id = b.id WHERE p.creation_date ".
                "BETWEEN ? AND ? AND b.organization_id = ? ".
                "AND p.payment_type = ?";

            $result = $this->db->query($query, array($day1, $day2, $organization_id, $showpaymenttype)) 
                or die ("paymentstatus 2 queryfailed");
        }

        return $result->result_array();
    }


    function distinctdeclined($day1, $day2, $organization_id)
    {
        if ($organization_id == "all") 
        {
            $query = "SELECT DISTINCT(p.creditcard_number), p.status, b.name, b.phone ".
                "FROM payment_history p ".
                "LEFT JOIN billing b ON p.billing_id = b.id ".
                "WHERE p.creation_date BETWEEN ? AND ? ".
                "AND p.payment_type = 'creditcard' AND p.status = 'declined'";

            $result = $this->db->query($query, array($day1, $day2))
                or die ("distinctdeclined 1 queryfailed");
        } 
        else 
        {
            $query = "SELECT DISTINCT(p.creditcard_number), p.status, b.name, b.phone ".
                "FROM payment_history p ".
                "LEFT JOIN billing b ON p.billing_id = b.id ".
                "WHERE p.creation_date BETWEEN ? AND ? ".
                "AND b.organization_id = ? ".
                "AND p.payment_type = 'creditcard' AND p.status = 'declined'";

            $result = $this->db->query($query, array($day1, $day2, $organization_id))
                or die ("distinctdeclined 2 queryfailed");
        }

        return $result->result_array();
    }


    function noncardpayments($day1, $day2, $organization_id)
    {
        // print the number of non-creditcard payments

        if ($organization_id == "all") 
        {
            $query = "SELECT p.*, b.name, b.phone FROM payment_history p ".
                "LEFT JOIN billing b ON p.billing_id = b.id WHERE p.creation_date ".
                "BETWEEN ? AND ? AND p.payment_type <> 'creditcard'";

            $result = $this->db->query($query, array($day1, $day2))
                or die ("noncardpayments 2 query failed");
        } 
        else 
        {
            $query = "SELECT p.*, b.name, b.phone FROM payment_history p ".
                "LEFT JOIN billing b ON p.billing_id = b.id WHERE p.creation_date ".
                "BETWEEN ? AND ? AND b.organization_id = ? ".
                "AND p.payment_type <> 'creditcard'";    

            $result = $this->db->query($query, array($day1, $day2, $organization_id))
                or die ("noncardpayments 2 query failed");
        }

        return $result->result_array();

    }


    /*
     * ------------------------------------------------------------------------
     *  get the list of services from the master_services table
     * ------------------------------------------------------------------------
     */
    function listservices()
    {
        $query = "SELECT * FROM master_services ORDER BY service_description";
        $result = $this->db->query($query) or die ("queryfailed");

        return $result->result_array();
    }


    function distinctservices($service_id)
    {
        // number of services added and billing type for each added
        $query = "SELECT DISTINCT us.id us_id, bi.id bi_id, bt.method bt_method, " .
            "cr.reason cancel_reason FROM user_services us " .
            "LEFT JOIN master_services ms ON ms.id = us.master_service_id " .
            "LEFT JOIN billing bi ON bi.id = us.billing_id " .
            "LEFT JOIN billing_types bt ON bt.id = bi.billing_type " .
            "LEFT JOIN customer cu ON us.account_number = cu.account_number " .
            "LEFT JOIN cancel_reason cr ON cu.cancel_reason = cr.id " .
            "WHERE ms.id = ?";
        $result = $this->db->query($query, array($service_id))
            or die ("distinctservices queryfailed");

        return $result->result_array();
    }


    /*
     * ------------------------------------------------------------------------
     *  services in this category added during date period with sources
     * ------------------------------------------------------------------------
     */
    function servicesources($day1, $day2, $category)
    {
        $query = "SELECT DISTINCT us.id us_id, cu.source " .
            "FROM user_services us " .
            "LEFT JOIN master_services ms ON ms.id = us.master_service_id " .
            "LEFT JOIN customer cu ON cu.account_number = us.account_number " .
            "WHERE ms.category = ? ".
            "AND date(us.start_datetime) BETWEEN ? AND ?";
        $result = $this->db->query($query, array($category, $day1, $day2))
            or die ("servicesources queryfailed");

        return $result->result_array();

    }


    function pastdueexempt()
    {
        $query = "SELECT * FROM billing WHERE pastdue_exempt = 'y'";
        $result = $this->db->query($query) or die ("pastdue exempt query failed");

        return $result->result_array();
    }


    function baddebt()
    {
        $query = "SELECT * FROM billing WHERE pastdue_exempt = 'bad_debt'";
        $result = $this->db->query($query) or die ("baddebt query failed");

        return $result->result_array();
    }


    function taxexempt()
    {
        $query = "SELECT tr.description, c.account_number, c.name, c.company, ".
            "te.customer_tax_id, ".
            "te.expdate FROM tax_exempt te ".
            "LEFT JOIN customer c ON c.account_number = te.account_number ".
            "LEFT JOIN tax_rates tr ON tr.id = tax_rate_id";
        $result = $this->db->query($query) or die ("tax exempt query failed");

        return $result->result_array();
    }


    function servicechurn($month, $year)
    {
        // initialize variables for services array
        $services = array();
        $i = 0;

        // get a total of customers for of all services at end of that month/year period
        $daysinmonth = date("t", mktime(0,0,0, $month, 1, $year));
        $firstofmonth = date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));
        $lastofmonth = date("Y-m-d", mktime(0, 0, 0, $month, $daysinmonth, $year));

        $query = "SELECT ms.category, ms.service_description, ms.id, count(*) AS monthtotal ".
            "FROM user_services us LEFT JOIN master_services ms ON ms.id = us.master_service_id ".
            "LEFT JOIN billing b ON b.id = us.billing_id ".
            "LEFT JOIN billing_types t ON t.id = b.billing_type ".
            "WHERE date(us.start_datetime) <= ? ".
            "AND ((date(us.end_datetime) >= ?) OR (us.removed <> 'y')) ".
            "AND ms.frequency > 0 AND t.method <> 'free' GROUP BY ms.id ORDER BY category";
        $totalresult = $this->db->query($query, array($lastofmonth, $firstofmonth))
            or die ("servicechurn select total customers queryfailed");
        foreach ($totalresult->result_array() AS $mytotalresult) 
        {
            $service_description = $mytotalresult['service_description'];
            $msid = $mytotalresult['id'];
            $totalformonth = $mytotalresult['monthtotal'];
            $category = $mytotalresult['category'];

            // search for customers with recurring service charges have an end_datetime in that month and year period
            //   us.end_datetime >= first of month AND us.end_datetime <= end of month
            $query = "SELECT count(*) AS count FROM user_services us ".
                "WHERE YEAR(us.end_datetime) = ? ".
                "AND MONTH(us.end_datetime) = ? AND us.master_service_id = ?";
            $endresult = $this->db->query($query, array($year, $month, $msid))
                or die ("servicechurn count services queryfailed");
            $myendresult = $endresult->row_array();
            $lostcount = $myendresult['count'];

            $percentchurn = sprintf("%.2f",($lostcount/$totalformonth)*100);

            $services[$i] = array(
                'service_description' => $service_description, 
                'category' => $category, 
                'lostcount' => $lostcount, 
                'totalformonth' => $totalformonth, 
                'percentchurn' => $percentchurn 
            );
            $i++;

        }

        // return the services array
        return $services;
    }

    function largecustomers($day1, $day2)
    {
        $query = "SELECT b.id, b.name, b.company, b.street, b.account_number, ".
            "bh.billing_date, ROUND(SUM(bh.new_charges),2) AS TotalCharges, ".
            "bh.id invoice, bh.new_charges, bh.from_date, bh.to_date ".
            "FROM billing_history bh ".
            "LEFT JOIN billing b ON bh.billing_id = b.id ".
            "LEFT JOIN customer cu ON cu.account_number = b.account_number ".
            "LEFT JOIN billing_types t ON b.billing_type = t.id ".
            "WHERE cu.cancel_date IS NULL AND t.method <> 'free' AND ".
            "bh.billing_date BETWEEN ? AND ? ".
            "GROUP BY bh.billing_id ORDER BY TotalCharges DESC LIMIT 20";

        $result = $this->db->query($query, array($day1, $day2)) or die ("query failed");

        return $result->result_array();
    }

}

/* end reports_model */
