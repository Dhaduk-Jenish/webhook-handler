<?php
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require_once(GetConfig('SITE_BASE_PATH').'/lib/stripe-php/stripe-config.php');	  // stripe
require_once(GetConfig('SITE_BASE_PATH').'/lib/stripe-php/init.php');	  // bpoint  // stripe
class CIT_WEBHOOK
{
	
	public function __construct()
	{
	}
	
	public function displayPage(){
		if($_REQUEST['category_id'] =='stripe'){ 
			$this->StripeWebhook(); exit;
		}
		else if($_REQUEST['category_id'] =='github'){ 
			$this->GitWebhook(); exit;
		}
		else if($_REQUEST['category_id'] =='custom'){ 
			$this->CustomWebhook(); exit;
		}

		$GLOBALS['CLA_HTML']->addMain($GLOBALS['WWW_TPL'].'/webhook.html');	
		$GLOBALS['CLA_HTML']->display();
		exit();	
	}
	
    private function StripeWebhook(){ 
		\Stripe\Stripe::setApiKey(GetConfig('STRIPE_SECRET_KEY'));
		$endpoint_secret = GetConfig('STRIPE_WEBHOOK_SECRET');  // test secret
		$payload = @file_get_contents('php://input');
	    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
		$event = null;

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload, $sig_header, $endpoint_secret
			);
		} catch(\UnexpectedValueException $e) {
			// Invalid payload
			echo "<script type='text/javascript'>alert('Not valid event');window.close();</script>";
			return http_response_code(400);
			exit();	
		} catch(\Stripe\Exception\SignatureVerificationException $e) {
			// Invalid signature
			echo "<script type='text/javascript'>alert('Not valid event try with valid data');window.close();</script>";
			return http_response_code(400);
			exit();	
		}
		//Saving webhook data to database
		$eventName = $event->type;
		$eventResponse = $event->data->object;
		$subscription = $eventResponse->lines->data[0];
		$userId =  $subscription->metadata->user_id;
		
		$data['user_id'] = $userId;
		$data['event_name'] = $eventName;
		$data['event_response'] = $eventResponse;
		
		$GLOBALS['DB']->insert("stripe_webhook",$data);
		
		
		switch ($event->type) {
			case 'invoice.payment_succeeded': // subscription create success
			 	$paymentIntent = $event->data->object;	
				$invoice_link =  $paymentIntent->invoice_pdf;
				$amount_paid =  $paymentIntent->amount_paid;
				$subscription = $paymentIntent->lines->data[0];	
				$subscription_id =  $subscription->subscription;
				$plan_id = $subscription->plan->id;
				$start_time = $subscription->period->start;
				$end_time = $subscription->period->end;
				$customer_id = $paymentIntent->customer;
				$customer_email = $paymentIntent->customer_email;
				$plan_id = $subscription->plan->id;
				$plan_interval = $subscription->plan->interval; 
				$userId =  $subscription->metadata->user_id;
				$planId =  $subscription->metadata->plan_id;
				$planUnit =  $subscription->metadata->plan_unit;
				$invoice_no = $paymentIntent->id;
				
				$memRow = $GLOBALS['DB']->row("SELECT * FROM registerusers RU LEFT JOIN registerusers_subscription SU ON RU.user_id = SU.user_id WHERE RU.user_id=?",array($userId));
					
				if($memRow['user_status'] == 0){
					$updateResult = $GLOBALS['DB']->update('registerusers',array('user_status' => 1),array('user_id'=>$memRow['user_id']));
					if($updateResult){
						$current_dir = GetConfig('SITE_UPLOAD_PATH') . "/signature/complete/".$userId."-expire";
						$rename_dir =  GetConfig('SITE_UPLOAD_PATH') . "/signature/complete/".$userId;
						rename($current_dir,$rename_dir);
					}
				}
				if($memRow['user_id']){
					$data = array('plan_id'=>$planId,'customer_id'=>$customer_id,'subscription_id' => $subscription_id,'price_id' =>$plan_id,'plan_interval' => $plan_interval,'period_start' => $start_time,'period_end' => $end_time,'invoice_link' => $invoice_link,'invoice_amount'=>$amount_paid,'plan_cancel'=>0);
					$where = array('user_id'=>$userId);
					$add = $GLOBALS['DB']->update('registerusers_subscription',$data,$where);
					
				}else{
					$data = array('plan_id'=>$planId,'customer_id'=>$customer_id,'subscription_id' => $subscription_id,'price_id' =>$plan_id,'plan_interval' => $plan_interval,'period_start' => $start_time,'period_end' => $end_time,'apply_coupon'=>$coupon_id,'invoice_link' => $invoice_link,'invoice_amount'=>$amount_paid,'plan_cancel'=>0);
					$add = $GLOBALS['DB']->insert("registerusers_subscription",$data);
				}
				$GLOBALS['amount_paid'] = GetPriceFormat($amount_paid / 100);
				$GLOBALS['payment_date'] = date('M d, Y');
				$GLOBALS['renew_date'] = date('M d, Y',$end_time);
				$GLOBALS['invoce_link'] = $invoice_link;
				$GLOBALS['plan_limit'] = $planUnit;
				//https://stripe.com/docs/api/invoices/object
			break;
			case 'invoice.payment_failed':
				
				$paymentIntent = $event->data->object;	
				$invoice_link =  $paymentIntent->invoice_pdf;
				$amount_paid =  $paymentIntent->amount_paid;
				$subscription = $paymentIntent->lines->data[0];	
				$subscription_id =  $subscription->subscription;
				$plan_id = $subscription->plan->id;
				$start_time = $subscription->period->start;
				$end_time = $subscription->period->end;
				$customer_id = $paymentIntent->customer;
				$customer_email = $paymentIntent->customer_email;
				$plan_id = $subscription->plan->id;
				$plan_interval = $subscription->plan->interval; 
				$puser_id =  $subscription->metadata->user_id;
				$planId =  $subscription->metadata->plan_id;
				$planUnit =  $subscription->metadata->plan_unit;
				$invoice_no = $paymentIntent->id;
				
				
				$updateResult = $GLOBALS['DB']->update('registerusers',array('user_planactive' => 0),array('user_id'=>$puser_id));
				if($updateResult){
					// plan canceled
					$GLOBALS['DB']->update('registerusers_subscription',array('plan_cancel'=>1),array('user_id'=>$puser_id));
					$current_dir = GetConfig('SITE_UPLOAD_PATH')."/signature/complete/".$puser_id;
					$rename_dir =  GetConfig('SITE_UPLOAD_PATH')."/signature/complete/".$puser_id.'-expire';
					rename($current_dir,$rename_dir);
					
					$userRow = $GLOBALS['DB']->row("SELECT `user_email`,`user_firstname` FROM `registerusers` WHERE user_id = ?",array($puser_id));
					if($userRow['user_email'] != ""){
						$GLOBALS['USERNAME'] = $userRow['user_firstname'];
						$customer_email = $userRow['user_email'];
					}
				}
				
								
				
				return http_response_code(200);
			break;
			case 'customer.subscription.created':
				$subscription = $event->data->object;	
				$subscriptionId = $subscription->id;
				$userId = $subscription->metadata->user_id;
				$free_trial = $subscription->status == 'trialing' ? 1 : 0;	
				$data = array('free_trial'=>$free_trial,'plan_cancel'=>0);
					$where = array('user_id'=>$userId);
					$add = $GLOBALS['DB']->update('registerusers_subscription',$data,$where);
				
				return http_response_code(200);
			break;
			case 'charge.refunded':
				$refundIntent = $event->data->object;
				
				if($refundIntent->refunded == true){ // check charge has been fully refunded	
					$customer_id = $refundIntent->customer;
					$userRow = $GLOBALS['DB']->row("SELECT * FROM `registerusers_subscription` US INNER JOIN registerusers RU ON US.user_id = RU.user_id WHERE US.customer_id = ? LIMIT 0,1",array($customer_id));
					if($userRow['user_id']){
						$puser_id = $userRow['user_id'];
						$updateResult = $GLOBALS['DB']->update('registerusers',array('user_planactive' => 0),array('user_id'=>$puser_id));
						if($updateResult){
							// plan canceled
							$GLOBALS['DB']->update('registerusers_subscription',array('plan_cancel'=>1,'plan_signaturelimit'=>1,'period_start'=>0,'period_end'=>0),array('user_id'=>$puser_id));
							$current_dir = GetConfig('SITE_UPLOAD_PATH')."/signature/complete/".$puser_id;
							$rename_dir =  GetConfig('SITE_UPLOAD_PATH')."/signature/complete/".$puser_id.'-expire';
							rename($current_dir,$rename_dir);
						}
					}
				}
				
				return http_response_code(200);
			break;
			
			case 'customer.subscription.updated': // subscription period end
				$subscription = $event->data->object;	
				$subscriptionId = $subscription->id;
				$userId = $subscription->metadata->user_id;
				$start_time =  $subscription->current_period_start;
				$end_time = $subscription->current_period_end;
				$free_trial = $subscription->status == 'trialing' ? 1 : 0;
				
				if($subscription->status == 'active' || $subscription->status == 'trialing'){	
					$data = array('free_trial'=>$free_trial,'plan_cancel'=>0,'period_start' => $start_time,'period_end' =>$end_time);
					$where = array('user_id'=>$userId);
					$add = $GLOBALS['DB']->update('registerusers_subscription',$data,$where);
					
					$current_dir = GetConfig('SITE_UPLOAD_PATH') . "/signature/complete/".$userId."-expire";
					$rename_dir =  GetConfig('SITE_UPLOAD_PATH') . "/signature/complete/".$userId;
					rename($current_dir,$rename_dir);
				}else{
					$data = array('free_trial'=>0,'plan_cancel'=>1,'period_start' =>0,'period_end'=>0);
					$where = array('user_id'=>$userId);
					$add = $GLOBALS['DB']->update('registerusers_subscription',$data,$where);
					
					$current_dir = GetConfig('SITE_UPLOAD_PATH')."/signature/complete/".$userId;
					$rename_dir =  GetConfig('SITE_UPLOAD_PATH')."/signature/complete/".$userId.'-expire';
					rename($current_dir,$rename_dir);
				}
				
				return http_response_code(200);
			break;
			
			case 'customer.subscription.deleted': // subscription period end
				$subscription = $event->data->object;	
				$subscriptionId = $subscription->id;
				$userId = $subscription->metadata->user_id;
				$message = 'S'.$memberId;

				$data = array('plan_id' => '','plan_interval' => '','period_start' => 0,'period_end' => 0,'plan_cancel'=>1);
				$where = array('user_id'=>$userId);
				$GLOBALS['DB']->update('registerusers_subscription',$data,$where);

				// update register table
					$data = array('user_planactive' => 0);
					$where = array('user_id'=>$userId);
					$GLOBALS['DB']->update('registerusers',$data,$where);
			break;
			
			case 'invoice.finalized': // invoice finalized stripe success
			 	$paymentIntent = $event->data->object;	
				$invoice_link =  $paymentIntent->invoice_pdf;
				$amount_paid =  $paymentIntent->amount_paid;
				$subscription = $paymentIntent->lines->data[0];	
				$subscription_id =  $subscription->subscription;
				$plan_id = $subscription->plan->id;
				$start_time = $subscription->period->start;
				$end_time = $subscription->period->end;
				$customer_id = $paymentIntent->customer;
				$customer_email = $paymentIntent->customer_email;
				$plan_id = $subscription->plan->id;
				$plan_interval = $subscription->plan->interval; 
				$userId =  $subscription->metadata->user_id;
				$planId =  $subscription->metadata->plan_id;
				$planUnit =  $subscription->metadata->plan_unit;
				$invoice_no = $paymentIntent->id;
				
				$memRow = $GLOBALS['DB']->row("SELECT * FROM registerusers RU LEFT JOIN registerusers_subscription SU ON RU.user_id = SU.user_id WHERE RU.user_id=?",array($userId));
					
				if($memRow['user_status'] == 0){
					$updateResult = $GLOBALS['DB']->update('registerusers',array('user_status' => 1),array('user_id'=>$memRow['user_id']));
					if($updateResult){
						$current_dir = GetConfig('SITE_UPLOAD_PATH') . "/signature/complete/".$userId."-expire";
						$rename_dir =  GetConfig('SITE_UPLOAD_PATH') . "/signature/complete/".$userId;
						rename($current_dir,$rename_dir);
					}
				}
				if($memRow['user_id']){
					$data = array('plan_id'=>$planId,'customer_id'=>$customer_id,'subscription_id' => $subscription_id,'price_id' =>$plan_id,'plan_interval' => $plan_interval,'period_start' => $start_time,'period_end' => $end_time,'invoice_link' => $invoice_link,'invoice_amount'=>$amount_paid,'plan_cancel'=>0);
					$where = array('user_id'=>$userId);
					$add = $GLOBALS['DB']->update('registerusers_subscription',$data,$where);
					
				}else{
					$data = array('plan_id'=>$planId,'customer_id'=>$customer_id,'subscription_id' => $subscription_id,'price_id' =>$plan_id,'plan_interval' => $plan_interval,'period_start' => $start_time,'period_end' => $end_time,'apply_coupon'=>$coupon_id,'invoice_link' => $invoice_link,'invoice_amount'=>$amount_paid,'plan_cancel'=>0);
					$add = $GLOBALS['DB']->insert("registerusers_subscription",$data);
				}
				//https://stripe.com/docs/api/invoices/object
			break;
			
			default:
			 	return http_response_code(200);
        		exit();
		}
		echo "<script type='text/javascript'>alert('stripe webhook run succcess!');window.close();</script>";

		//_SendMail('dhvlpatel906@gmail.com',1,'Sstripe Webhook',$message); 
		return http_response_code(200);
	}
	
	private function GitWebhook(){

		$postData = $_POST['git_data'];
		$postData['commit_id'] = 'asfmk35jjnvd90th5n6048n0';
		$postData['commit_message'] = 'Webhook file changes';
		$postData['commit_filelist'] = 'index.php, include/*, lib/*';
		if($postData){
			$data['commit_id'] = $postData['commit_id'];
			$data['commit_message'] = $postData['commit_message'];
			$data['commit_filelist'] = $postData['commit_filelist'];

			$GLOBALS['DB']->insert("git_commit_data",$data);
			echo "<script type='text/javascript'>alert('github webhook run succcess!(with test static data)');window.close();</script>";
			return http_response_code(200);
			exit();
		}else{
			echo "<script type='text/javascript'>alert('something went wrong with post data!');window.close();</script>";
			return http_response_code(400);
			exit();
		}
	}

	private function CustomWebhook(){

		$postData = $_POST['git_data'];
		$postData['user_name'] = 'Jenish Dhaduk';
		$postData['user_email'] = 'jenishdhaduk99@gmail.com';
		$postData['user_password'] = 'asdjsaiufhruiehuivnbuirhebu674785yh6ui5n6huinvf87gv';
		if($postData){
			$data['user_name'] = $postData['user_name'];
			$data['user_email'] = $postData['user_email'];
			$data['user_password'] = $postData['user_password'];

			$GLOBALS['DB']->insert("registerusers",$data);
			echo "<script type='text/javascript'>alert('Custom webhook run succcess and user registred succcess!(with test static data)');window.close();</script>";
		}else{
			echo "<script type='text/javascript'>alert('something went wrong with post data!');window.close();</script>";

		}
	}
	
}

?>