<?php

namespace app\controllers;
if (!isset($_SESSION)) {
    session_start();
}

use Yii;
use app\models\Transaction;
use app\models\Payment;
use app\models\PaymentSearch;
use app\models\TransactionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
use app\models\Commissionsub;
use app\models\Commission;
use app\model\Cconfig;

use kartik\mpdf\Pdf;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends Controller
{
    public function behaviors()
    {
        return [
             'access' => [
                'class' => AccessControl::classname(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                 //   'delete' => ['post'],
                ],
            ],
            
        ];
    }

    
    public function beforeAction($event) {
        $this->enableCsrfValidation = false;
         if(Yii::$app->Permission->getPermission())
            return parent::beforeAction($event);
        else
            $this->redirect(['site/permission']);
      //  $this->enableCsrfValidation = false;
       // return parent::beforeAction($action);
    }

     
    public function actionAdvance_ajustment()
    {
        $connection = Yii::$app->getDb();
        if(isset($_POST) && !empty($_POST))
        {
            if($_POST['type']==1)
            {
                for($i=1;$i<=($_POST['tq']);$i++)
                {
                    if($_POST['ad_id'.$i]>0 && $_POST['qamount'.$i]>0 && $_POST['v'.$i]>0)
                    {
                        $rem=$_POST['v'.$i]-$_POST['qamount'.$i];

                        $ad  = "SELECT * from payment where id='".$_POST['ad_id'.$i]."'"; 
			            $adr = $connection->createCommand($ad)->queryOne();
                        
                        if($adr['amount']>$_POST['qamount'.$i])
                        {
                            
                            $sql = "Insert INTO payment 
                            (`referanceid`, `pfor`, `vid`, `acno`, `amount`, `amount1`, `date`, `remarks`, `type`, 
                            `salecenter`, `branch_id`, `cost_center`, `jvid`, `sid`, `pro_id`, `ptyid`, `status`,
                             `createdate`, `reConcile`, `msno`, `party`, `gl`, `user_id`)
                            SELECT 
                            `referanceid`, `pfor`, `vid`, `acno`, `amount`, `amount1`, `date`, `remarks`, `type`, 
                            `salecenter`, `branch_id`, `cost_center`, `jvid`, `sid`, `pro_id`, `ptyid`, `status`,
                             `createdate`, `reConcile`, `msno`, `party`, `gl`, `user_id`
                            from payment where id='".$_POST['ad_id'.$i]."'";
                            \Yii::$app->db->createCommand($sql)->execute();
                            $id=Yii::$app->db->getLastInsertID();

                            $sql = "UPDATE payment SET amount='".($adr['amount']-$_POST['qamount'.$i])."'
                            WHERE id='".$id."' ";
                            \Yii::$app->db->createCommand($sql)->execute();

                        }

                        $sql = "UPDATE payment SET pro_id='".$_POST['vid'.$i]."' 
                        WHERE id='".$_POST['ad_id'.$i]."' ";
                        \Yii::$app->db->createCommand($sql)->execute();
                    }
                }
            }
            if($_POST['type']==2)
            {
                for($i=1;$i<=($_POST['tq']);$i++)
                {
                    if($_POST['ad_id'.$i]>0 && $_POST['qamount'.$i]>0 && $_POST['v'.$i]>0)
                    {
                        $rem=$_POST['v'.$i]-$_POST['qamount'.$i];

                        $ad  = "SELECT * from payment where id='".$_POST['ad_id'.$i]."'"; 
			            $adr = $connection->createCommand($ad)->queryOne();
                        
                        if($adr['amount1']>$_POST['qamount'.$i])
                        {
                            $sql = "Insert INTO payment 
                            (`referanceid`, `pfor`, `vid`, `acno`, `amount`, `amount1`, `date`, `remarks`, `type`, 
                            `salecenter`, `branch_id`, `cost_center`, `jvid`, `sid`, `pro_id`, `ptyid`, `status`,
                             `createdate`, `reConcile`, `msno`, `party`, `gl`, `user_id`)
                            SELECT 
                            `referanceid`, `pfor`, `vid`, `acno`, `amount`, `amount1`, `date`, `remarks`, `type`, 
                            `salecenter`, `branch_id`, `cost_center`, `jvid`, `sid`, `pro_id`, `ptyid`, `status`,
                             `createdate`, `reConcile`, `msno`, `party`, `gl`, `user_id`
                            from payment where id='".$_POST['ad_id'.$i]."'";
                            \Yii::$app->db->createCommand($sql)->execute();
                            $id=Yii::$app->db->getLastInsertID();

                            $sql = "UPDATE payment SET amount1='".($adr['amount1']-$_POST['qamount'.$i])."'
                            WHERE id='".$id."' ";
                            \Yii::$app->db->createCommand($sql)->execute();

                        } 

                        $sql = "UPDATE payment SET sid='".$_POST['vid'.$i]."',amount1='".$_POST['qamount'.$i]."'
                        WHERE id='".$_POST['ad_id'.$i]."' ";
                        \Yii::$app->db->createCommand($sql)->execute();
                    }
                }
            }
            return 1;

        }
        else
        {
            return $this->render('advance_adjustment');
        }
    }
    public function actionSalespad()
	{
    
          $connection = Yii::$app->getDb(); 
          if(isset($_REQUEST['val'])){
            $and = 'AND payment.id = "'.$_REQUEST['val'].'"';
          } else {
            $and = '';
          }
            
            
            
          $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
          $acc1 = $connection->createCommand($acc)->queryOne(); 
        

        $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' and   
        remarks !='Receivable Amount' AND vid = '".$acc1['ref']."' $and ORDER BY `date` ";      
        $amr = $connection->createCommand($am)->queryAll();     
        $i=0;$j=0;
        if(isset($_REQUEST['tq']) && $_REQUEST['tq']>0)
        {
            $i = $_REQUEST['tq'];
        }
        
        $arr = array();
        $html='';
        foreach ($amr as $row)
        { 
            $amm  = "SELECT SUM(amount1) as am from payment where sid='".$row['id']."' 
            AND referanceid='".$_REQUEST['mid']."' ";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $row['amount'] - $amrm['am'];
            if($r>0)
            { 
                $temp = array();
                $i=$i+1;
            
            $html.='<tr>
                <td>'.$i.'
                    <input type="hidden" name="vid'.$i.'" style="width:100px;height: 20px;" readonly="readonly" 
                    value="'.$row['id'].'"/>
                </td>
                <td>'.$row['date'].'</td>
                <td>'.$row['remarks'].'
                    <input type="hidden" name="rem'.$i.'" id="rem'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value="'.$row['remarks'].'" />
                </td>
                <td>'.number_format($r,2).'
                    <input type="hidden" name="v'.$i.'" id="v'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value='.$r.' />

                    <input type="hidden" name="ad_id'.$i.'" id="ad_id'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value="0" />
                </td>
                <td>
                    <input oninput="this.value=this.value.replace(/[^0-9]/g,0)"  type="text" 
                    style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;"
                    onblur="tamount('.$i.')" name="qamount'.$i.'" id="qamount'.$i.'"
                    value="0" />
                </td>
            </tr>';
            }   
        }   
        $html.='<tr style="display: none;">
            <td>
                <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="'.$i.'" />
            </td>
        </tr>';
        return $html;
    } 
    public function actionSalespad1()
	{
    
        $connection = Yii::$app->getDb(); 

        $am1  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' 
        and (ptyid IS NULL || ptyid='' || ptyid=0) AND remarks!='Discount'
        $and and  (sid IS NULL || sid='' || sid=0) and amount1>0 ORDER BY `date` ";      
        $am1r = $connection->createCommand($am1)->queryAll(); 
        $html='';
        foreach ($am1r as $row)
        {  
                $i=$i+1;
                $style='';
                if($i>1){$style="pointer-events: none;";}
            
            $html.='<tr style="'.$style.'"> 
                <td>'.$row['remarks'].'</td>  
                <td> '.$row['amount1'].' </td>
                <td>
                
                <input type="hidden" name="adv_id'.$i.'" id="adv_id'.$i.'" value='.$row['id'].' />
                <input type="hidden" name="ad_am'.$i.'" id="ad_am'.$i.'" value='.$row['amount1'].' />

                <input oninput="this.value=this.value.replace(/[^0-9]/g,0)"  type="text" 
                style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;"
                name="qamountad'.$i.'" id="qamountad'.$i.'" value="0" onBlur="divide_advance()"/>
            </td>
                </td>
            </tr>'; 
        }   
        $html.='<tr style="display: none;">
            <td>
                <input type="hidden" style="width:100px;height: 20px;" name="tqad" id="tqad" value="'.$i.'" />
            </td>
        </tr>';
        return $html;
    } 
    public function actionPurspcad()
	{
    
          $connection = Yii::$app->getDb(); 
          if(isset($_REQUEST['val'])){
            $and = 'AND payment.id = "'.$_REQUEST['val'].'"';
          } else {
            $and = '';
          }
            
            
            
          $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
          $acc1 = $connection->createCommand($acc)->queryOne(); 
        

         $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' 
        and (ptyid IS NULL || ptyid='' || ptyid=0)
        $and ORDER BY `date` ";      
        $amr = $connection->createCommand($am)->queryAll();     
        $i=0;$j=0;
        if(isset($_REQUEST['tq']) && $_REQUEST['tq']>0)
        {
            $i = $_REQUEST['tq'];
        }
        
        $arr = array();
        $html='';
        foreach ($amr as $row)
        { 
            $amm  = "SELECT SUM(amount) as am from payment where pro_id='".$row['id']."' 
            AND referanceid='".$_REQUEST['mid']."'  ";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $row['amount1'] - $amrm['am'];
            if($r>0)
            { 
                $temp = array();
                $i=$i+1;
            
            $html.='<tr>
                <td>'.$i.'
                    <input type="hidden" name="vid'.$i.'" style="width:100px;height: 20px;" readonly="readonly" 
                    value="'.$row['id'].'"/>
                </td>
                <td>'.$row['date'].'</td>
                <td>'.$row['remarks'].'
                    <input type="hidden" name="rem'.$i.'" id="rem'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value="'.$row['remarks'].'" />
                </td>
                <td>'.number_format($r,2).'
                    <input type="hidden" name="v'.$i.'" id="v'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value='.$r.' />

                    <input type="hidden" name="ad_id'.$i.'" id="ad_id'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value="0" />
                </td>
                <td>
                    <input oninput="this.value=this.value.replace(/[^0-9]/g,0)"  type="text" 
                    style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;"
                    onblur="tamount('.$i.')" name="qamount'.$i.'" id="qamount'.$i.'" readonly
                    value="0" />
                </td>
            </tr>';
            }   
        }   
        $html.='<tr style="display: none;">
            <td>
                <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="'.$i.'" />
            </td>
        </tr>';
        return $html;
    } 
    public function actionPurspcad1()
	{
    
        $connection = Yii::$app->getDb(); 

        $am1  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' 
        and (ptyid IS NULL || ptyid='' || ptyid=0)
        $and and  (pro_id IS NULL || pro_id='' || pro_id=0) and amount>0 ORDER BY `date` ";      
        $am1r = $connection->createCommand($am1)->queryAll(); 
        $html='';
        foreach ($am1r as $row)
        {  
                $i=$i+1;
                $style='';
                if($i>1){$style="pointer-events: none;";}
            
            $html.='<tr style="'.$style.'"> 
                <td>'.$row['remarks'].'</td>  
                <td> '.$row['amount'].' </td>
                <td>
                
                <input type="hidden" name="adv_id'.$i.'" id="adv_id'.$i.'" value='.$row['id'].' />
                <input type="hidden" name="ad_am'.$i.'" id="ad_am'.$i.'" value='.$row['amount'].' />

                <input oninput="this.value=this.value.replace(/[^0-9]/g,0)"  type="text" 
                style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;"
                name="qamountad'.$i.'" id="qamountad'.$i.'" value="0" onBlur="divide_advance()"/>
            </td>
                </td>
            </tr>'; 
        }   
        $html.='<tr style="display: none;">
            <td>
                <input type="hidden" style="width:100px;height: 20px;" name="tqad" id="tqad" value="'.$i.'" />
            </td>
        </tr>';
        return $html;
    } 
    
	public function actionApprove_payment()
    {
        $i=0;	
        $conn=Yii::$app->getDb();
	    $co=count($_POST['as']);
        do 
        {
		    if(isset($_POST['as'][$i]))
		    { 			    
                $id=$_POST['as'][$i];
			    if($_POST['action']=='trans_payment')
			    {
			        $sql="Update transaction SET isApp='1' where id='".$id."'";
    			    \Yii::$app->db->createCommand($sql)->execute();    
                    
                    $am  = "SELECT * from payment where vid='".$id."' 
                    AND (pfor=1 || pfor=6 || pfor=10)";
                    $amr = $conn->createCommand($am)->queryAll(); 
                    foreach ($amr as $amr1)
                    {
                        $sql1="Update payment SET gl='1' where id='".$amr1['id']."'";
                        \Yii::$app->db->createCommand($sql1)->execute();  
                    }
			    }			    
			    if($_POST['action']=='charges_payment')
			    {
                    $am  = "SELECT * from payment where 
                    (payment.id='".$id."' || payment.msno='".$id."')";
                    $amr = $conn->createCommand($am)->queryAll(); 
                    foreach ($amr as $amr1)
                    {
                        $sql1="Update payment SET gl='1' where id='".$amr1['id']."'";
                        \Yii::$app->db->createCommand($sql1)->execute();  
                    }
                }		    
			    if($_POST['action']=='commission_payments')
			    {
                    $am  = "SELECT * from payment where 
                    payment.vid='".$id."' AND pfor=10 AND gl=0";
                    $amr = $conn->createCommand($am)->queryAll(); 
                    foreach ($amr as $amr1)
                    {
                        $sql1="Update payment SET gl='1' where id='".$amr1['id']."'";
                        \Yii::$app->db->createCommand($sql1)->execute();  
                    }
                }			    
			    if($_POST['action']=='ms_payments')
			    {
                    
			        $sql="Update memberplot SET fstatus='Approved' where id='".$id."'";
    			    \Yii::$app->db->createCommand($sql)->execute();   
                }	    
			    if($_POST['action']=='payroll_payments')
			    {                    
			        $sql="Update salary SET fstatus='1' where id='".$id."'";
    			    \Yii::$app->db->createCommand($sql)->execute();  

                    $am  = "SELECT * from payment where 
                    payment.vid='".$id."' AND pfor=7";
                    $amr = $conn->createCommand($am)->queryAll(); 
                    foreach ($amr as $amr1)
                    {
                        $sql1="Update payment SET gl='1' where id='".$amr1['id']."'";
                        \Yii::$app->db->createCommand($sql1)->execute();  
                    }
                }	    
			    if($_POST['action']=='invoice_payments')
			    {                    
			        $sql="Update trans SET fstatus='1' where trans_id='".$id."'";
    			    \Yii::$app->db->createCommand($sql)->execute(); 

                    $am  = "SELECT * from payment where 
                    payment.vid='".$id."' AND pfor=3";
                    $amr = $conn->createCommand($am)->queryAll(); 
                    foreach ($amr as $amr1)
                    {
                        $sql1="Update payment SET gl='1' where id='".$amr1['id']."'";
                        \Yii::$app->db->createCommand($sql1)->execute();  
                    }
                }

		    }
		    $i++;
	    }while($co > $i);	    
        return $this->redirect('index.php?r=transaction/dashboard');       
    }
    public function actionUpdate_payment_form()
    {
        $conn=Yii::$app->getDb();
        for($i=1;$i<=$_POST['total'];$i++)
        {
            if(isset($_POST['acc_id'.$i]) && $_POST['acc_id'.$i]>0)
            {
                if($_POST['debit'.$i]==''){$_POST['debit'.$i]=0;}
                if($_POST['credit'.$i]==''){$_POST['credit'.$i]=0;}
                $sql="Update payment SET referanceid='".$_POST['acc_id'.$i]."',date='".$_POST['date'.$i]."' ,
                amount='".$_POST['debit'.$i]."',amount1='".$_POST['credit'.$i]."' 
                where id='".$_POST['pid'.$i]."'";
                \Yii::$app->db->createCommand($sql)->execute(); 
            }
        }
	    
             
    }
    public function actionTrans_payments()
    {
        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->orderBy(['id' => SORT_DESC,]);
        
        if($_SESSION["user_array"]["usertype"]==2)
		{
		$dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
		}
		 
		$dataProvider->query->andFilterWhere(['IN','transaction.isApp',[0]]);
		
		$dataProvider->pagination->pageSize=$request->get("pagesize",50);
        if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
		$dataProvider->pagination->page=($_REQUEST['page']-1);
		

        if($request->isAjax)
        {
            return $this->renderPartial('trans_payments', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else
        {   $searchModel1='';
      $dataProvider1='';
            return $this->render('trans_payments', [
                'searchModel1' => $searchModel1,
                'dataProvider1' => $dataProvider1,
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    } 
	public function actionCheck_transactions()
    {
	    $co=count($_POST['as']);
        $error="";
        for($i=0;$i<=$co;$i++)
        {
            $_POST['as'][$i];
            if(isset($_POST['as'][$i]))
		    {
                $connection = Yii::$app->getDb();	

                $debit=0;
                $credit=0;
                $j=0;
                $vno='';

                $trans  = "SELECT vno,payment.amount,payment.amount1,payment.date,payment.referanceid from payment
                Join transaction ON (transaction.id=payment.vid AND payment.pfor IN (1,6))
                where vid='".$_POST['as'][$i]."' AND pfor IN (1,6)";
                $transr = $connection->createCommand($trans)->queryAll(); 
                foreach($transr as $row)
                {
                    $j++;
                    $vno=$row['vno'];

                    if($j>1)
                    {
                        if($date!=$row['date'])
                        {
                            $error.="Date is not matched in Voucher #".$vno."\n"; 
                        }
                    }

                    $sqlac="SELECT acc.* From accounts acc
                    Left Join accounts acc1 ON (acc1.id=acc.accounttype)
                    where acc1.accounttype>0 AND acc.id='".$row['referanceid']."'";
                    $sqlacr = $connection->createCommand($sqlac)->queryOne(); 

                    if($sqlacr['id']>0)
                    {}
                    else
                    {
                        $error.="Account is not selected in Voucher #".$vno."\n"; 
                    }

                    $debit=$debit+$row['amount'];
                    $credit=$credit+$row['amount1'];

                    $date=$row['date'];
                }
                if($debit!=$credit)
                {
                    $error.="Debit and Credit not matched\n";
                }
                if($j==0)
                {                    
                    $error.="No Entries Available\n";
                }
            }
        }
        if(empty($error))
        {
            return 1;
        }
        else
        {
            return $error;
        }
    }        
    public function actionPayment_entries($vid)
    {
        $connection = Yii::$app->getDb();	 
        $res1='';
        $i=0;
        $sqlac="SELECT acc.* From accounts acc
        Left Join accounts acc1 ON (acc1.id=acc.accounttype)
        where acc1.accounttype>0";
        $sqlacr = $connection->createCommand($sqlac)->queryAll(); 

        

        $trans  = "SELECT * from payment 
        where vid='".$vid."' AND pfor IN (1,6)";
        $transr = $connection->createCommand($trans)->queryAll(); 
        foreach($transr as $row)
        {
            $i++;
            $ac='';
            foreach($sqlacr as $row1)
            {
                $sel="";
                if($row1['id']==$row['referanceid']){$sel="selected";}
                $ac.='<option '.$sel.' value="'.$row1['id'].'">'.$row1['name'].'</option>';
            }
            $res1.='<div class="col-xs-12" style="margin-top:4%;"> 
            <div class="col-xs-3"> 
                <label>Account</label>
                <select name="acc_id'.$i.'" id="acc_id'.$i.'" class="chzn-select" style="margin-top: 4px;width: 100%;">
                    <option value="">Select Account</option>
                    '.$ac.'
                </select>
            </div>
            <div class="col-xs-3"> 
                <label>Date</label>
                <input type="text" name="date'.$i.'" id="date'.$i.'" value="'.$row['date'].'" 
                style="border: 0px;border-bottom: 1px dotted;" class="date-picker1"/>
            </div>
            <div class="col-xs-3"> 
                <label>Debit</label>
                <input type="text" name="debit'.$i.'" id="debit'.$i.'" value="'.$row['amount'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            <div class="col-xs-3"> 
                <label>Credit</label>
                <input type="text" name="credit'.$i.'" id="credit'.$i.'" value="'.$row['amount1'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            
            <input type="hidden" name="pid'.$i.'" id="pid'.$i.'" value="'.$row['id'].'"/>
            </div>';
        }

        $res='<form id="update_payment_form" name="update_payment_form">
            <div class="row">            
                '.$res1.'
                <div class="col-xs-12" style="margin-top:3%;">  
                    <div class="col-xs-12">  
                        <input type="hidden" name="total" id="total" value="'.$i.'"/>
                        <input type="button" onClick="check_app()" value="Update"  
                        class="btn-success pull-right"/>
                    </div>
                </div> 
            </div>    
        </form>';
        return $res;    
    }

	public function actionApprove_charges_payments()
    { 
        return $this->render('approve_charges_payments' );
    } 
    public function actionCheck_charges()
    {
	    $co=count($_POST['as']);
        $error="";
        for($i=0;$i<=$co;$i++)
        {
            $_POST['as'][$i];
            if(isset($_POST['as'][$i]))
		    {
                $connection = Yii::$app->getDb();	

                $debit=0;
                $credit=0;
                $j=0;
                $vno='';

                $trans  = "SELECT  amount,amount1,date,referanceid  from payment 
                WHERE (payment.id='".$id."' || payment.msno='".$id."')";
                $transr = $connection->createCommand($trans)->queryAll(); 
                foreach($transr as $row)
                {
                    $j++;
                    // $vno=$row['vno'];

                    if($j>1)
                    {
                        if($date!=$row['date'])
                        {
                            $error.="Date is not matched\n"; 
                        }
                    }

                    $debit=$debit+$row['amount'];
                    $credit=$credit+$row['amount1'];

                    $date=$row['date'];

                    $sqlac="SELECT acc.* From accounts acc
                    Left Join accounts acc1 ON (acc1.id=acc.accounttype)
                    where acc1.accounttype>0 AND acc.id='".$row['referanceid']."'";
                    $sqlacr = $connection->createCommand($sqlac)->queryOne(); 

                    if($sqlacr['id']>0)
                    {}
                    else
                    {
                        $error.="Account is not selected\n"; 
                    }
                }
                if($debit!=$credit)
                {
                    $error.="Debit and Credit not matched\n";
                }
            }
        }
        if(empty($error))
        {
            return 1;
        }
        else
        {
            return $error;
        }
    }
	public function actionCharges_entries($id)
    {
        $connection = Yii::$app->getDb();	 
        $res1='';
        $i=0;
        $sqlac="SELECT acc.* From accounts acc
        Left Join accounts acc1 ON (acc1.id=acc.accounttype)
        where  acc1.accounttype>0";
        $sqlacr = $connection->createCommand($sqlac)->queryAll(); 

        

        $trans  = "SELECT payment.id as pid,amount,amount1,date,accounts.id as aid,name from payment
        Join accounts ON (accounts.id=payment.referanceid)
        WHERE (payment.id='".$id."' || payment.msno='".$id."')"; 
        $transr = $connection->createCommand($trans)->queryAll(); 
        foreach($transr as $row)
        {
            $i++;
            $ac='';
            foreach($sqlacr as $row1)
            {
                $sel="";
                if($row1['id']==$row['aid']){$sel="selected";}
                $ac.='<option '.$sel.' value="'.$row1['id'].'">'.$row1['name'].'</option>';
            }
            $res1.='<div class="col-xs-12" style="margin-top:4%;"> 
            <div class="col-xs-3"> 
                <label>Account</label>
                <select name="acc_id'.$i.'" id="acc_id'.$i.'" class="chzn-select" style="margin-top: 4px;width: 100%;">
                    <option value="">Select Account</option>
                    '.$ac.'
                </select>
            </div>
            <div class="col-xs-3"> 
                <label>Date</label>
                <input type="text" name="date'.$i.'" id="date'.$i.'" value="'.$row['date'].'" 
                style="border: 0px;border-bottom: 1px dotted;" class="date-picker1"/>
            </div>
            <div class="col-xs-3"> 
                <label>Debit</label>
                <input type="text" name="debit'.$i.'" id="debit'.$i.'" value="'.$row['amount'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            <div class="col-xs-3"> 
                <label>Credit</label>
                <input type="text" name="credit'.$i.'" id="credit'.$i.'" value="'.$row['amount1'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            
            <input type="hidden" name="pid'.$i.'" id="pid'.$i.'" value="'.$row['pid'].'"/>
            </div>';
        }

        $res='<form id="update_payment_form" name="update_payment_form">
            <div class="row">            
                '.$res1.'
                <div class="col-xs-12" style="margin-top:3%;">  
                    <div class="col-xs-12">  
                        <input type="hidden" name="total" id="total" value="'.$i.'"/>
                        <input type="button" onClick="check_app()" value="Update"  
                        class="btn-success pull-right"/>
                    </div>
                </div> 
            </div>    
        </form>';
        return $res;    
    } 

	public function actionApprove_commission_payments()
    { 
        return $this->render('approve_commission_payments' );
    } 
    public function actionCheck_commission()
    {
	    $co=count($_POST['as']);
        $error="";
        for($i=0;$i<=$co;$i++)
        {
            $_POST['as'][$i];
            if(isset($_POST['as'][$i]))
		    {
                $connection = Yii::$app->getDb();	

                $debit=0;
                $credit=0;
                $j=0;
                $vno='';

                $trans  = "SELECT plotno,payment.amount,payment.amount1,payment.date,payment.referanceid from payment
                Join commission ON (commission.id=payment.vid)
                Join accounts ON (commission.mid=accounts.id)
                Join memberplot ON (commission.mem_id=memberplot.id)
                where vid='".$_POST['as'][$i]."'  AND pfor=10 AND payment.status=1";
                $transr = $connection->createCommand($trans)->queryAll(); 
                foreach($transr as $row)
                {
                    $j++;
                    $vno=$row['plotno'];

                    if($j>1)
                    {
                        if($date!=$row['date'])
                        {
                            $error.="Date is not matched in MS #".$vno."\n"; 
                        }
                    }

                    $debit=$debit+$row['amount'];
                    $credit=$credit+$row['amount1'];

                    $date=$row['date'];

                    

                    $sqlac="SELECT acc.* From accounts acc
                    Left Join accounts acc1 ON (acc1.id=acc.accounttype)
                    where acc1.accounttype>0 AND acc.id='".$row['referanceid']."'";
                    $sqlacr = $connection->createCommand($sqlac)->queryOne(); 

                    if($sqlacr['id']>0)
                    {}
                    else
                    {
                        $error.="Account is not selected in MS #".$vno."\n"; 
                    }

                }
                if($debit!=$credit)
                {
                    $error.="Debit and Credit not matched in MS #".$vno."\n";
                }
            }
        }
        if(empty($error))
        {
            return 1;
        }
        else
        {
            return $error;
        }
    }
	public function actionCommission_entries($id)
    {
        $connection = Yii::$app->getDb();	 
        $res1='';
        $i=0;
        $sqlac="SELECT acc.* From accounts acc
        Left Join accounts acc1 ON (acc1.id=acc.accounttype)
        where  acc1.accounttype>0";
        $sqlacr = $connection->createCommand($sqlac)->queryAll(); 

        

        $trans  = "SELECT payment.id as pid,amount,amount1,date,accounts.id as aid,name from payment
        Join accounts ON (accounts.id=payment.referanceid)
        WHERE payment.vid='".$id."' AND pfor=10 AND status=1"; 
        $transr = $connection->createCommand($trans)->queryAll(); 
        foreach($transr as $row)
        {
            $i++;
            $ac='';
            foreach($sqlacr as $row1)
            {
                $sel="";
                if($row1['id']==$row['aid']){$sel="selected";}
                $ac.='<option '.$sel.' value="'.$row1['id'].'">'.$row1['name'].'</option>';
            }
            $res1.='<div class="col-xs-12" style="margin-top:4%;"> 
            <div class="col-xs-3"> 
                <label>Account</label>
                <select name="acc_id'.$i.'" id="acc_id'.$i.'" class="chzn-select" style="margin-top: 4px;width: 100%;">
                    <option value="">Select Account</option>
                    '.$ac.'
                </select>
            </div>
            <div class="col-xs-3"> 
                <label>Date</label>
                <input type="text" name="date'.$i.'" id="date'.$i.'" value="'.$row['date'].'" 
                style="border: 0px;border-bottom: 1px dotted;" class="date-picker1"/>
            </div>
            <div class="col-xs-3"> 
                <label>Debit</label>
                <input type="text" name="debit'.$i.'" id="debit'.$i.'" value="'.$row['amount'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            <div class="col-xs-3"> 
                <label>Credit</label>
                <input type="text" name="credit'.$i.'" id="credit'.$i.'" value="'.$row['amount1'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            
            <input type="hidden" name="pid'.$i.'" id="pid'.$i.'" value="'.$row['pid'].'"/>
            </div>';
        }

        $res='<form id="update_payment_form" name="update_payment_form">
            <div class="row">            
                '.$res1.'
                <div class="col-xs-12" style="margin-top:3%;">  
                    <div class="col-xs-12">  
                        <input type="hidden" name="total" id="total" value="'.$i.'"/>
                        <input type="button" onClick="check_app()" value="Update"  
                        class="btn-success pull-right"/>
                    </div>
                </div> 
            </div>    
        </form>';
        return $res;    
    } 

    public function actionApprove_ms_payments()
    { 
        return $this->render('approve_ms_payments' );
    } 
    public function actionCheck_ms()
    {
	    $co=count($_POST['as']);
        $error="";
        for($i=0;$i<=$co;$i++)
        {
            if(isset($_POST['as'][$i]))
		    {
                $id=$_POST['as'][$i];
                $connection = Yii::$app->getDb();	

                $debit=0;
                $credit=0;
                $j=0;
                $vno='';

                $trans  = "SELECT plotno,payment.amount,payment.amount1,payment.date,payment.referanceid from payment
                Join memberplot ON (memberplot.id=payment.vid AND payment.pfor IN (2))
                WHERE payment.vid='".$id."' AND pfor=2 AND (payment.party is NULL || payment.party='') 
                AND payment.remarks!='Discount' AND payment.gl=1";
                $transr = $connection->createCommand($trans)->queryAll(); 
                foreach($transr as $row)
                {
                    $j++;
                    $vno=$row['plotno'];

                    if($j>1)
                    {
                        if($date!=$row['date'])
                        {
                            $error.="Date is not matched in MS #".$vno."\n"; 
                        }
                    }

                    $debit=$debit+$row['amount'];
                    $credit=$credit+$row['amount1'];

                    $date=$row['date'];

                    

                    $sqlac="SELECT acc.* From accounts acc
                    Left Join accounts acc1 ON (acc1.id=acc.accounttype)
                    where  acc1.accounttype>0 AND acc.id='".$row['referanceid']."'";
                    $sqlacr = $connection->createCommand($sqlac)->queryOne(); 

                    if($sqlacr['id']>0)
                    {}
                    else
                    {
                        $error.="Account is not selected in MS #".$vno."\n"; 
                    }
                }
                if($debit!=$credit)
                {
                    $error.="Debit and Credit not matched in MS #".$vno."\n";
                }
            }
        }
        if(empty($error))
        {
            return 1;
        }
        else
        {
            return $error;
        }
    }
    public function actionMs_entries($id)
    {
        $connection = Yii::$app->getDb();	 
        $res1='';
        $i=0;
        $sqlac="SELECT acc.* From accounts acc
        Left Join accounts acc1 ON (acc1.id=acc.accounttype)
        where  acc1.accounttype>0";
        $sqlacr = $connection->createCommand($sqlac)->queryAll(); 

        

        $trans  = "SELECT payment.id as pid,amount,amount1,date,accounts.id as aid,name from payment
        Join accounts ON (accounts.id=payment.referanceid)
        WHERE payment.vid='".$id."' AND pfor=2 AND (payment.party is NULL || payment.party='') 
        AND payment.remarks!='Discount' AND payment.gl=1"; 
        $transr = $connection->createCommand($trans)->queryAll(); 
        foreach($transr as $row)
        {
            $i++;
            $ac='';
            foreach($sqlacr as $row1)
            {
                $sel="";
                if($row1['id']==$row['aid']){$sel="selected";}
                $ac.='<option '.$sel.' value="'.$row1['id'].'">'.$row1['name'].'</option>';
            }
            $res1.='<div class="col-xs-12" style="margin-top:4%;"> 
            <div class="col-xs-3"> 
                <label>Account</label>
                <select name="acc_id'.$i.'" id="acc_id'.$i.'" class="chzn-select" style="margin-top: 4px;width: 100%;">
                    <option value="">Select Account</option>
                    '.$ac.'
                </select>
            </div>
            <div class="col-xs-3"> 
                <label>Date</label>
                <input type="text" name="date'.$i.'" id="date'.$i.'" value="'.$row['date'].'" 
                style="border: 0px;border-bottom: 1px dotted;" class="date-picker1"/>
            </div>
            <div class="col-xs-3"> 
                <label>Debit</label>
                <input type="text" name="debit'.$i.'" id="debit'.$i.'" value="'.$row['amount'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            <div class="col-xs-3"> 
                <label>Credit</label>
                <input type="text" name="credit'.$i.'" id="credit'.$i.'" value="'.$row['amount1'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            
            <input type="hidden" name="pid'.$i.'" id="pid'.$i.'" value="'.$row['pid'].'"/>
            </div>';
        }

        $res='<form id="update_payment_form" name="update_payment_form">
            <div class="row">            
                '.$res1.'
                <div class="col-xs-12" style="margin-top:3%;">  
                    <div class="col-xs-12">  
                        <input type="hidden" name="total" id="total" value="'.$i.'"/>
                        <input type="button" onClick="check_app()" value="Update"  
                        class="btn-success pull-right"/>
                    </div>
                </div> 
            </div>    
        </form>';
        return $res;    
    } 
    public function actionApprove_payroll_payments()
    { 
        return $this->render('approve_payroll_payments' );
    } 
    public function actionCheck_payroll()
    {
	    $co=count($_POST['as']);
        $error="";
        for($i=0;$i<=$co;$i++)
        {
            $_POST['as'][$i];
            if(isset($_POST['as'][$i]))
		    {
                $connection = Yii::$app->getDb();	

                $debit=0;
                $credit=0;
                $j=0;
                $vno='';

                $trans  = "SELECT slipno,payment.amount,payment.amount1,payment.date,payment.referanceid from payment
                Join salary ON (salary.id=payment.vid AND payment.pfor IN (7))
                where vid='".$_POST['as'][$i]."' AND pfor IN (7)";
                $transr = $connection->createCommand($trans)->queryAll(); 
                foreach($transr as $row)
                {
                    $j++;
                    $vno=$row['slipno'];

                    if($j>1)
                    {
                        if($date!=$row['date'])
                        {
                            $error.="Date is not matched in Slip #".$vno."\n"; 
                        }
                    }

                    $debit=$debit+$row['amount'];
                    $credit=$credit+$row['amount1'];

                    $date=$row['date'];
                      

                    $sqlac="SELECT acc.* From accounts acc
                    Left Join accounts acc1 ON (acc1.id=acc.accounttype)
                    where acc1.accounttype>0 AND acc.id='".$row['referanceid']."'";
                    $sqlacr = $connection->createCommand($sqlac)->queryOne(); 

                    if($sqlacr['id']>0)
                    {}
                    else
                    {
                        $error.="Account is not selected in Slip #".$vno."\n"; 
                    }

                }
                if($debit!=$credit)
                {
                    $error.="Debit and Credit not matched in Slip #".$vno."\n";
                }
            }
        }
        if(empty($error))
        {
            return 1;
        }
        else
        {
            return $error;
        }
    }
	public function actionPayroll_entries($id)
    {
        $connection = Yii::$app->getDb();	 
        $res1='';
        $i=0;
        $sqlac="SELECT acc.* From accounts acc
        Left Join accounts acc1 ON (acc1.id=acc.accounttype)
        where  acc1.accounttype>0";
        $sqlacr = $connection->createCommand($sqlac)->queryAll(); 

        

        $trans  = "SELECT payment.id as pid,amount,amount1,date,accounts.id as aid,name from payment
        Join accounts ON (accounts.id=payment.referanceid)
        WHERE payment.vid='".$id."' AND pfor=7"; 
        $transr = $connection->createCommand($trans)->queryAll(); 
        foreach($transr as $row)
        {
            $i++;
            $ac='';
            foreach($sqlacr as $row1)
            {
                $sel="";
                if($row1['id']==$row['aid']){$sel="selected";}
                $ac.='<option '.$sel.' value="'.$row1['id'].'">'.$row1['name'].'</option>';
            }
            $res1.='<div class="col-xs-12" style="margin-top:4%;"> 
            <div class="col-xs-3"> 
                <label>Account</label>
                <select name="acc_id'.$i.'" id="acc_id'.$i.'" class="chzn-select" style="margin-top: 4px;width: 100%;">
                    <option value="">Select Account</option>
                    '.$ac.'
                </select>
            </div>
            <div class="col-xs-3"> 
                <label>Date</label>
                <input type="text" name="date'.$i.'" id="date'.$i.'" value="'.$row['date'].'" 
                style="border: 0px;border-bottom: 1px dotted;" class="date-picker1"/>
            </div>
            <div class="col-xs-3"> 
                <label>Debit</label>
                <input type="text" name="debit'.$i.'" id="debit'.$i.'" value="'.$row['amount'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            <div class="col-xs-3"> 
                <label>Credit</label>
                <input type="text" name="credit'.$i.'" id="credit'.$i.'" value="'.$row['amount1'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            
            <input type="hidden" name="pid'.$i.'" id="pid'.$i.'" value="'.$row['pid'].'"/>
            </div>';
        }

        $res='<form id="update_payment_form" name="update_payment_form">
            <div class="row">            
                '.$res1.'
                <div class="col-xs-12" style="margin-top:3%;">  
                    <div class="col-xs-12">  
                        <input type="hidden" name="total" id="total" value="'.$i.'"/>
                        <input type="button" onClick="check_app()" value="Update"  
                        class="btn-success pull-right"/>
                    </div>
                </div> 
            </div>    
        </form>';
        return $res;    
    }
    
    public function actionApprove_invoice_payments()
    { 
        return $this->render('approve_invoice_payments' );
    } 
    public function actionCheck_invoice()
    {
	    $co=count($_POST['as']);
        $error="";
        for($i=0;$i<=$co;$i++)
        {
            $_POST['as'][$i];
            if(isset($_POST['as'][$i]))
		    {
                $connection = Yii::$app->getDb();	

                $debit=0;
                $credit=0;
                $j=0;
                $vno='';

                $trans  = "SELECT invoice_no,payment.amount,payment.amount1,payment.date,payment.referanceid from payment
                Join trans ON (trans.trans_id=payment.vid AND payment.pfor IN (3))
                where vid='".$_POST['as'][$i]."' AND pfor IN (3)";
                $transr = $connection->createCommand($trans)->queryAll(); 
                foreach($transr as $row)
                {
                    $j++;
                    $vno=$row['invoice_no'];

                    if($j>1)
                    {
                        if($date!=$row['date'])
                        {
                            $error.="Date is not matched in Invoice #".$vno."\n"; 
                        }
                    }

                    $debit=$debit+$row['amount'];
                    $credit=$credit+$row['amount1'];

                    $date=$row['date'];

                    $sqlac="SELECT acc.* From accounts acc
                    Left Join accounts acc1 ON (acc1.id=acc.accounttype)
                    where  acc1.accounttype>0 AND acc.id='".$row['referanceid']."'";
                    $sqlacr = $connection->createCommand($sqlac)->queryOne(); 

                    if($sqlacr['id']>0)
                    {}
                    else
                    {
                        $error.="Account is not selected in Invoice #".$vno."\n"; 
                    }
                }
                if($debit!=$credit)
                {
                    $error.="Debit and Credit not matched in Invoice #".$vno."\n";
                }
            }
        }
        if(empty($error))
        {
            return 1;
        }
        else
        {
            return $error;
        }
    }      
    public function actionInvoice_entries($id)
    {
        $connection = Yii::$app->getDb();	 
        $res1='';
        $i=0;
        $sqlac="SELECT acc.* From accounts acc
        Left Join accounts acc1 ON (acc1.id=acc.accounttype)
        where  acc1.accounttype>0";
        $sqlacr = $connection->createCommand($sqlac)->queryAll(); 

        

        $trans  = "SELECT payment.id as pid,amount,amount1,date,accounts.id as aid,name from payment
        Join accounts ON (accounts.id=payment.referanceid)
        WHERE payment.vid='".$id."' AND pfor=3"; 
        $transr = $connection->createCommand($trans)->queryAll(); 
        foreach($transr as $row)
        {
            $i++;
            $ac='';
            foreach($sqlacr as $row1)
            {
                $sel="";
                if($row1['id']==$row['aid']){$sel="selected";}
                $ac.='<option '.$sel.' value="'.$row1['id'].'">'.$row1['name'].'</option>';
            }
            $res1.='<div class="col-xs-12" style="margin-top:4%;"> 
            <div class="col-xs-3"> 
                <label>Account</label>
                <select name="acc_id'.$i.'" id="acc_id'.$i.'" class="chzn-select" style="margin-top: 4px;width: 100%;">
                    <option value="">Select Account</option>
                    '.$ac.'
                </select>
            </div>
            <div class="col-xs-3"> 
                <label>Date</label>
                <input type="text" name="date'.$i.'" id="date'.$i.'" value="'.$row['date'].'" 
                style="border: 0px;border-bottom: 1px dotted;" class="date-picker1"/>
            </div>
            <div class="col-xs-3"> 
                <label>Debit</label>
                <input type="text" name="debit'.$i.'" id="debit'.$i.'" value="'.$row['amount'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            <div class="col-xs-3"> 
                <label>Credit</label>
                <input type="text" name="credit'.$i.'" id="credit'.$i.'" value="'.$row['amount1'].'"  
                style="border: 0px;border-bottom: 1px dotted;"/>
            </div>
            
            <input type="hidden" name="pid'.$i.'" id="pid'.$i.'" value="'.$row['pid'].'"/>
            </div>';
        }

        $res='<form id="update_payment_form" name="update_payment_form">
            <div class="row">            
                '.$res1.'
                <div class="col-xs-12" style="margin-top:3%;">  
                    <div class="col-xs-12">  
                        <input type="hidden" name="total" id="total" value="'.$i.'"/>
                        <input type="button" onClick="check_app()" value="Update"  
                        class="btn-success pull-right"/>
                    </div>
                </div> 
            </div>    
        </form>';
        return $res;    
    } 

    public function actionSalesp()
	{
        
        $connection = Yii::$app->getDb(); 
        if(isset($_REQUEST['val'])){
            $and = 'AND payment.id = "'.$_REQUEST['val'].'"';
        } else {
            $and = '';
        }
        
        $atype_sql = "SELECT p.atype FROM memberplot mp
        LEFT JOIN plots p ON (p.id = mp.plot_id)
        LEFT JOIN accounts acc ON (acc.ref = mp.id AND acc.type = 1)
        WHERE acc.id = '". $_REQUEST['mid'] ."'";
        $atype_res = $connection->createCommand($atype_sql)->queryOne();
        
        $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
        $acc1 = $connection->createCommand($acc)->queryOne();

        $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' and   
        vid = '".$acc1['ref']."' $and ORDER BY `date` ";
        $amr = $connection->createCommand($am)->queryAll();
        $i=0;$j=0;
        if(isset($_REQUEST['tq']) && $_REQUEST['tq']>0)
        {
            $i = $_REQUEST['tq'];
        }
        
        $arr = array();
        $html='';
        foreach ($amr as $row)
        {
            $amm  = "SELECT SUM(amount1) as am from payment where pfor In (1,6)
            AND referanceid='".$_REQUEST['mid']."' AND jvid > 0";
            $amrm = $connection->createCommand($amm)->queryOne();
            if ( $row['party'] != '' && $row['party'] > 0 )
            {
                $amm1  = "SELECT SUM(amount1) as am from payment where pfor In (1,6)
                AND referanceid='".$_REQUEST['mid']."' AND sid='".$row['id']."'";
                $amrm1 = $connection->createCommand($amm1)->queryOne();
                
                if (strpos($atype_res['atype'], "Gratis") !== false) {
                    $r = $row['amount'];
                } else {
                    $r = $row['amount'] - $amrm1['am'];
                }
                
            }
            else
            {
                if (strpos($atype_res['atype'], "Gratis") !== false) {
                    $r = $row['amount'];
                } else {
                    $r = $row['amount'] - $amrm['am'];
                }
            }
            if($r>0)
            {
                $temp = array();
                $i=$i+1;
                $status=0;
                if($row['remarks']=='Receivable Amount'){$row['remarks']="Receivable Against Plot/File";$status=1;}
            
                $html.='<tr>
                    <td>'.$i.'
                        <input type="hidden" name="vid'.$i.'" style="width:100px;height: 20px;" readonly="readonly" 
                        value="'.$row['id'].'"/>
                    </td>
                    <td>'.$row['date'].'</td>
                    <td>'.$row['remarks'].'
                        <input type="hidden" name="rem'.$i.'" id="rem'.$i.'" style="width:100px;height: 20px;"
                        readonly="readonly" value="'.$row['remarks'].'" />
                    </td>
                    <td>'.number_format($r,2).'
                        <input type="hidden" name="v'.$i.'" id="v'.$i.'" style="width:100px;height: 20px;"
                        readonly="readonly" value='.$r.' />
                        
                        <input type="hidden" name="status'.$i.'" id="status'.$i.'" style="width:100px;height: 20px;"
                        readonly="readonly" value='.$status.' />
                    </td>
                    <td>
                        <input oninput="this.value=this.value.replace(/[^0-9]/g,0)"  type="text" 
                        style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;"
                        onblur="tamount('.$i.')" name="qamount'.$i.'" id="qamount'.$i.'"
                        value="0" onkeyup="amount_to_words();">
                    </td>
                </tr>';
            }
        }   
        $html.='<tr style="display: none;">
            <td>
                <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="'.$i.'" />
            </td>
        </tr>';
        return $html;
    }  

     

    public function actionPurspc()
	{
    
          $connection = Yii::$app->getDb(); 
          if(isset($_REQUEST['val'])){
            $and = 'AND payment.id = "'.$_REQUEST['val'].'"';
          } else {
            $and = '';
          }
            
            
            
          $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
          $acc1 = $connection->createCommand($acc)->queryOne(); 
        

         $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' 
        and (ptyid IS NULL || ptyid='' || ptyid=0)
        $and ORDER BY `date` ";      
        $amr = $connection->createCommand($am)->queryAll();     
        $i=0;$j=0;
        if(isset($_REQUEST['tq']) && $_REQUEST['tq']>0)
        {
            $i = $_REQUEST['tq'];
        }
        
        $arr = array();
        $html='';
        foreach ($amr as $row)
        { 
            $amm  = "SELECT SUM(amount) as am from payment where pro_id='".$row['id']."' 
            AND referanceid='".$_REQUEST['mid']."'  ";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $row['amount1'] - $amrm['am'];
            if($r>0)
            { 
                $temp = array();
                $i=$i+1;
            
            $html.='<tr>
                <td>'.$i.'
                    <input type="hidden" name="vid'.$i.'" style="width:100px;height: 20px;" readonly="readonly" 
                    value="'.$row['id'].'"/>
                </td>
                <td>'.$row['date'].'</td>
                <td>'.$row['remarks'].'
                    <input type="hidden" name="rem'.$i.'" id="rem'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value="'.$row['remarks'].'" />
                </td>
                <td>'.number_format($r,2).'
                    <input type="hidden" name="v'.$i.'" id="v'.$i.'" style="width:100px;height: 20px;"
                    readonly="readonly" value='.$r.' />
                </td>
                <td>
                    <input oninput="this.value=this.value.replace(/[^0-9]/g,0)"  type="text" 
                    style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;"
                    onblur="tamount('.$i.')" name="qamount'.$i.'" id="qamount'.$i.'"
                    value="0" />
                </td>
            </tr>';
            }   
        }   
        $html.='<tr style="display: none;">
            <td>
                <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="'.$i.'" />
            </td>
        </tr>';
        return $html;
    } 

    
    

    public function a()
	{
    
        $connection = Yii::$app->getDb(); 
        if(isset($_REQUEST['val']))
        {
            $and = 'AND payment.id = "'.$_REQUEST['val'].'"';
        } 
        else 
        {
            $and = '';
        }
            
        
        $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
        $acc1 = $connection->createCommand($acc)->queryOne();
       
        $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' 
        $and ORDER BY `date` ";
        $amr = $connection->createCommand($am)->queryAll();     

        $i=0;$j=0;

        if(isset($_REQUEST['tq']) && $_REQUEST['tq']>0)
        {
            $i = $_REQUEST['tq'];
        }
        
        $arr = array();

        foreach ($amr as $row)
        { 
            $amm  = "SELECT SUM(amount) as am from payment where pro_id='".$row['id']."' 
            AND referanceid='".$_REQUEST['mid']."' ";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $row['amount1'] - $amrm['am'];
            if($r>0)
            { 
                $temp = array();
                $i=$i+1;
            ?>
<tr>
    <td>
        <?php 
                     echo $i;
                    ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $row['id']?>" />
    </td>
    <td><?php echo $row['date'];?>
    <td><?php echo $row['remarks'];?>
        <input type="hidden" name="rem<?php echo $i;?>" id="rem<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $row['remarks']?>" />
    </td>
    <td>
        <?php echo  number_format($r,2) ?>
        <input type="hidden" name="v<?php echo $i;?>" id="v<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $r?>" />
    </td>
    <td>
        <input oninput="this.value=this.value.replace(/[^0-9]/g,'')" type="text"
            style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;" onblur="tamount('<?php echo $i;?>')"
            name="qamount<?php echo $i;?>" id="qamount<?php echo $i;?>" value="0" />
    </td>
</tr>
<?php 
            }
        }   
        ?>
<tr style="display: none;">
    <td>
        <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
    </td>
</tr>
<?php      
  }

    public function actionSearchchall()
    {
        $connection = Yii::$app->getDb();
        $q = $_POST['data']['q'];
         $am  = "SELECT acc.*,plotno,m.name AS member_name,app_no,mp.status as mp_status,plots.status as p_status From accounts acc
        Left Join accounts acc1 ON (acc1.id=acc.accounttype)
        Left Join memberplot mp ON (mp.id=acc.ref AND acc.type=1)
        Left Join plots  ON (mp.plot_id=plots.id)
        LEFT JOIN members m ON (m.id=mp.member_id)
        where acc.type IN (1,2,3) AND acc1.type=3 AND acc1.accounttype>0 AND  (acc.name Like '%".$q."%' || plotno Like '%".$q."%')";
        $amr = $connection->createCommand($am)->queryAll(); 
        $return='';
        
       
        
        foreach ($amr as $row)
        {
             $status='';
        if($row['p_status']=='Merge Request'){
            $status= 'Merged';
        }else{
            $status='';
        }
            
            $t='';
            $name='';
            if(!empty($row['member_name'])){
                $name=$row['member_name'];
            }else{
                $name=$row['name'];
            }
           $results[] = array(
                'id' => $row['id'], 
                'text' => $row['app_no'] . ' - ' . $row['plotno'] . ' - ' . $name . ' - (' . $status . ')'
            );


        } 
        echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
        exit;
    }
    public function actionSearchchallaccount()
    {
          $connection = Yii::$app->getDb();
      $q = $_POST['data']['q'];
      $am  = "SELECT *,accounts.id as aid from accounts
      where (accounts.name Like '%".$q."%' ) AND type=3";
      $amr = $connection->createCommand($am)->queryAll(); 
      $return='';
    
                foreach ($amr as $row){$t='';
                
      $results[] = array('id' => $row['aid'], 'text' => $row['code'].' - '.$row['name']);
      } 
      echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
      }


    public function actionSearchch()
	{
       	$connection = Yii::$app->getDb();
       	
       	$where = '';
       	if ( isset( $_REQUEST['current_ms'] ) && !empty ( $_REQUEST['current_ms'] ) && $_REQUEST['current_ms'] > 0 ) {
       	    $where .= " AND accounts.id = '". $_REQUEST['current_ms'] ."'";
       	}
       	
        $q = $_POST['data']['q'];
        $am  = "SELECT *,accounts.id as aid from accounts
	    LEFT JOIN memberplot on (accounts.ref=memberplot.id and accounts.type=1)
		where  memberplot.status !='Cancel'AND (accounts.name Like '%".$q."%' or memberplot.plotno Like '%".$q."%') $where";
		$amr = $connection->createCommand($am)->queryAll();	
		$return='';
		
        foreach ($amr as $row)
        { 
            $t=$row['plotno'];
 			$results[] = array('id' => $row['aid'], 'text' => $t.' - '.$row['name']);
		}	
		echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
    }

    public function actionSearchch1()
	{
       	$connection = Yii::$app->getDb();
		$q = $_POST['data']['q'];
		$am  = "SELECT * from accounts where name Like '%".$q."%' AND type in (2,4,5)"; 
		$amr = $connection->createCommand($am)->queryAll();	
		
        foreach ($amr as $row)
        {
            $t='';
            if($row['type']==1)
            {
                $amm  = "SELECT * from memberplot where id='".$row['ref']."'"; 
			    $amrm = $connection->createCommand($amm)->queryOne();	
                
                $t=$amrm['plotno'];
            }
            if($row['type']==2)
            {
                $t='SS';
            }
            if($row['type']==4)
            {
                $t='EE';
            }
            $results[] = array('id' => $row['id'], 'text' => $t.' - '.$row['name']);
		}	
		echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
    }

    public function actionSearchchmember()
    {
        $connection = Yii::$app->getDb();
        $q = $_POST['data']['q'];
        $am  = "SELECT *,accounts.id as aid from accounts
        left join memberplot on (accounts.ref=memberplot.id and accounts.type=1)
        where (accounts.name Like '%".$q."%' or memberplot.plotno Like '%".$q."%') AND type=1";
        $amr = $connection->createCommand($am)->queryAll(); 
        $return='';
        foreach ($amr as $row)
        { 
            $t=$row['plotno'];
            $results[] = array('id' => $row['aid'], 'text' => $t.' - '.$row['name']);
        } 
        echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
    }
    

    public function actionFormcreate()
    { 
        $model = new Transaction();
        return $this->render('formcreate', ['model' => $model]);    
    }
    
    public function actionDashboard()
    {  
        return $this->render('dashboard');    
    }
    
    public function actionAll_payments()
    {
        return $this->render('all_payments');    
    }
    
    public function actionApp_detail()
    {
        return $this->render('app_detail');    
    }


    public function actionCreate()
    {
        $model = new Transaction();
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            }
            $model->fc=0;
            $model->rate=0;

            $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
            and vtype='".$model->vtype."'";
            $result_max = $connection->createCommand($sql_max)->queryOne();
            $model->vno=($result_max['MAX(vno)']+1);
            
            if($model->vtype==3){$model->bank_id=$_POST['ref'];}
            if($model->save())
            {  
                $ref=$model->bank_id;
                $insert_id=$model->id;
                $balance=$model->amount;
                if(isset($_POST['tq']) && $_POST['tq'] > 0)
 				{
                    
                        $q=1;
		                do  
                        {
                            if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && 
                            !empty($_POST['vid'.$q]))
						    {          
                                $comm1 = Commissionsub::find()->where(['pid' => $_POST['vid'.$q], 'status' => NULL])
                                ->all();           
                                foreach ($comm1 as $comm) 
                                { 
                                    if($comm->amount != '' || $comm->amount != 0)
                                    { 
                                        $com = Commission::find()->where(['id' => $comm->comid])->one(); 
                                        $sqltsa2 = "SELECT * from cconfig where type=17";
                                        $config = $connection->createCommand($sqltsa2)->queryOne();
              
                                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                        referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vid'.$q].", 
                                        pfor='10',amount='".$comm->amount."',date='".$model->create_date."',status='1',
                                        remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',
                                        type='0',jvid='0',gl='0',branch_id='".$model->branch_id."'";
                                        \Yii::$app->db->createCommand($sql)->execute();
             
                                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                        referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vid'.$q].", 
                                        pfor='10',amount1='".$comm->amount."',date='".$model->create_date."',status='1',
                                        remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',
                                        type='0',jvid='0',gl='0',branch_id='".$model->branch_id."'";
                                        \Yii::$app->db->createCommand($sql)->execute();
                  
                                        $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";
                                        \Yii::$app->db->createCommand($sql)->execute();
                                    }
                                }
                                $status=0;
                                if($_POST['status'.$q]==1){$status=1;}
                                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                referanceid='".$_POST['states-select']."',vid='".$insert_id."',sid=".$_POST['vid'.$q].", 
                                pfor='1',amount1='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',
                                remarks='".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='".$status."',
                                gl='0',branch_id='".$model->branch_id."'";
			        		    \Yii::$app->db->createCommand($sql)->execute();

                                $balance=$balance-$_POST['qamount'.$q];
							}							
							$q=$q+1;
						}while($q < ($_POST['tq']+1));				
					
                }
                if($balance>0)
				{ 
                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                    vid=".$insert_id.", pfor='1',amount1='".($balance)."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute(); 
				}
                        
                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                vid=".$insert_id.",sid=0, pfor='1',amount='".($model->amount)."',date='".$model->create_date."',
                status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                gl='0',branch_id='".$model->branch_id."'";
                \Yii::$app->db->createCommand($sql)->execute();	

                $mobile=0;
                $ref='';$chk='';
                if($model->vtype==2 || $model->vtype==4)
                {
                    $ref='Ref # '.$model['vno'];
                    $chk='Cheque # '.$model['cheque_no'];
                }
                
                $sql_phone = "SELECT *  from accounts
                Left Join memberplot ON (memberplot.id=accounts.ref AND accounts.type=1)
                Left Join members ON (members.id=memberplot.member_id)
                where accounts.id='".$model->pt_id."'";
                $result_phone = $connection->createCommand($sql_phone)->queryOne();
                
                if(!empty($result_phone['phone']))
                {
                    $mb_array=array();
                    $mb_array = explode(",", $result_phone['phone']);
                            
                    for($i=0;$i<count($mb_array);$i++)
                    {
                        $mb_array_i=0;
                        if(strlen($mb_array[$i])>9 && strlen($mb_array[$i])<=15)
                        {
                            $mb_array_i=str_replace(["-", "–"], '', $mb_array[$i]);   
                            $mobile='+92'.(substr($mb_array_i, -10));
                            $message='Dear Member, 
Thank you for your payment of PKR '.number_format($model->amount,0).'/-   for Your MS # '.$result_phone['plotno'].' '.$ref.' '.$chk.'
Your ledger has been updated and can be verified online.
Contact Us at : 03041118888';
                            if($model->create_date==date('Y-m-d'))
                            {
                                // Yii::$app->mycomponent->Send_text_msg($mobile,$message);
                            }
                        }
                    }
                }  
			
				// $a='Receive Installments';
    //             $at='Create';
    //             $u=$insert_id;
    //             $d = '';
    //             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                
                $a='15'; // Receive Installments ID in system_activities
                $at='Create';
                $u= $insert_id;
                
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$_POST['states-select']."' AND type=1 ")->queryOne();
                
                $details = "VoucherDate: ".$model->create_date.",ReceiptType: $voucher_type, Receipt#: ".$model->receipt.",
                            Amount: ".$model->amount.", Narration: ".$model->remarks." ";
                Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$memberplot['ref']);
            
            

            } 
            return 1; 
        } 
        else
        {
            return 0;
        }       

    }

    public function actionIns_recieved()
    {	
		if(isset($_REQUEST['page']) && $_REQUEST['page']>0)
		{
			$connection = Yii::$app->getDb();
			function findper($search, $ref, $value)
            {
                $keys=array();
                foreach($search as $key)
                {
                    if($key[$ref]==$value) 
                    { 
                        return $key;
                    }
                }
                 
            }
        
            $pay = findper($_SESSION['user_perm_array'], 'module_id', 116);  
            $new=false;
            $save=false;
            $remove=false;
            if($pay['new']==1){$new=true;}
            if($pay['save']==1){$save=true;}
            if($pay['remove']==1){$remove=true;}
            
			$page = 1;
			if(!empty($_REQUEST["page"])) 
			{
				$page = $_REQUEST["page"];
			}
			
			$start = ($page-1)*20;
			if($start < 0) $start = 0;
			
			$where='';
			if($_SESSION["user_array"]["usertype"]==2)
		    {
		        $where.=" AND transaction.user_id='".$_SESSION["user_array"]["id"]."'";        
		    }
			if(isset($_POST['date']) && !empty($_POST['date']))
			{
			    $where.=" AND transaction.create_date='".$_POST['date']."'";
			}
			if(isset($_POST['vno']) && !empty($_POST['vno']))
			{
			    $where.=" AND transaction.receipt='".$_POST['vno']."'";
			}
			if(isset($_POST['msno']) && !empty($_POST['msno']))
			{
			    $where.=" AND memberplot.plotno Like '%".$_POST['msno']."%'";
			}
			if(isset($_POST['customer']) && !empty($_POST['customer']))
			{
			    $where.=" AND members.name Like '%".$_POST['customer']."%'";
			}
			if(isset($_POST['dealer']) && !empty($_POST['dealer']))
			{
			    $where.=" AND to_id.id='".$_POST['dealer']."'";
			}
			if(isset($_POST['block']) && !empty($_POST['block']))
			{
			    $where.=" AND plots.sector='".$_POST['block']."'";
			}
			if(isset($_POST['floor']) && !empty($_POST['floor']))
			{
			    $where.=" AND plots.street_id='".$_POST['floor']."'";
			}
			if(isset($_POST['unit']) && !empty($_POST['unit']))
			{
			    $where.=" AND plots.plot_detail_address='".$_POST['unit']."'";
			} 
			
			
			$sqlt = "SELECT * from transaction
			Where transaction.status_type=0 AND vtype IN (3,4) ";
    	    $resultt = $connection->createCommand($sqlt)->query();
    	    
    		$_POST["rowcount"] = count($resultt);
// 			
// 			Left Join accounts to_id ON (transaction.cash_id=to_id.id OR transaction.bank_id=to_id.id)
			$sql = "SELECT transaction.create_date,transaction.receipt,transaction.amount,from_id.name as fname,transaction.status_type,transaction.vtype ,transaction.id, 
			plots.plot_detail_address,streets.street,sectors.sector_name,transaction.isApp,app_no,plotno,members.name as mname,cnic,memberplot.status as mp_status from transaction
			Left Join accounts from_id ON (transaction.pt_id=from_id.id)
			Left Join memberplot ON (from_id.ref=memberplot.id AND from_id.type=1)
			Left Join members ON (members.id=memberplot.member_id)
			Left Join plots ON (plots.id=memberplot.plot_id)
			Left Join streets ON (streets.id=plots.street_id)
			Left Join sectors ON (plots.sector=sectors.id)
			Where transaction.status_type=0 AND vtype IN (3,4) $where Order By transaction.id DESC  Limit $start,20"; 
			$result = $connection->createCommand($sql)->query();
// 			print_r($result); exit;
			
		
			$paginationlink = "index.php?r=transaction/ins_recieved&page=";	
			$pagination_setting = "all-links"; 
			$perpageresult = Yii::$app->mycomponent->Pagination($_POST["rowcount"], $paginationlink);
			
			$output = '';
			foreach($result as $row) 
			{
			?>
<tr>
    <td align="center" style="width: 7%;"><?php echo $row['create_date'] ?></td>
    <td align="center" style="width: 7%;"><?php echo $row['receipt'] ?></td>
    <td align="center" style="width: 14%;"><?php echo $row['plotno']; ?></td>
    <td align="center" style="width: 14%;"><?php echo $row['app_no']; ?></td>
    <td align="center" style="width: 14%;"><?php echo $row['mname']; ?></td>
    <td align="center" style="width: 14%;"><?php echo $row['cnic']; ?></td>
    <td align="center" style="width: 7%;"><?php echo number_format($row['amount'],0) ?></td>
    <td class="center" style="width: 6%;">
        <?php if($row['isApp']==0 || $_SESSION['user_array']['user_level']==1){ ?>
        <?php if($save){ ?>

        <?php if($row['mp_status'] == 'Cancel') { ?>
        <a href="javascript:void(0);" class="tooltip-error"
            onclick="alert('This is a Merged File. Kindly Revert the File First For Updation!.');"
            title="This is a merged file. Kindly Revert the File First.">
            <span class="blue">
                <i class="ace-icon fa fa-pencil bigger-120" style="color: grey;"></i> <!-- Greyed out icon -->
            </span>
        </a>
        <?php } else { ?>
        <a data-original-title="Edit"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/update_installment&id=<?php echo $row['id']; ?>"
            class="tooltip-error ajaxlink" data-rel="tooltip" title="">
            <span class="blue">
                <i class="ace-icon fa fa-pencil bigger-120"></i>
            </span>
        </a>
        <?php } ?>



        <?php } ?>
        <?php if($remove){ ?>

        <?php if ($row['mp_status'] == 'Cancel') { ?>
        <a onclick="alert('This is a Merged File.  Kindly Revert the File First For Deletion!'); return false;"
            data-original-title="Delete" href="javascript:void(0);" class="tooltip-error deletelink" data-rel="tooltip"
            title="">
            <span class="red">
                <i class="ace-icon fa fa-trash-o bigger-120"></i>
            </span>
        </a>
        <?php } else { ?>
        <a onclick="return confirm('Are you sure you want to delete?');" data-original-title="Delete"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/delete&id=<?php echo $row['id']; ?>"
            class="tooltip-error deletelink" data-rel="tooltip" title="">
            <span class="red">
                <i class="ace-icon fa fa-trash-o bigger-120"></i>
            </span>
        </a>
        <?php } ?>




        <?php } ?>

        <?php } ?>
        <?php if($row['vtype']==1 or $row['vtype']==3){ ?>
        <a target="_blank" data-original-title="Print"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/insprints&id=<?php echo $row['id']; ?>&type=1"
            class="tooltip-error deletelink" data-rel="tooltip" title=""> <span class="green"> <i
                    class="ace-icon fa fa-print"></i> </span> </a>
        <?php } ?>
        <?php if($row['vtype']==2 or $row['vtype']==4){ ?>
        <a target="_blank" data-original-title="Print"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/insprints&id=<?php echo $row['id']; ?>&type=2"
            class="tooltip-error deletelink" data-rel="tooltip" title=""> <span class="green"> <i
                    class="ace-icon fa fa-print"></i> </span> </a>
        <?php } ?>
        <a target="_blank" data-original-title="Print"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/insprints&id=<?php echo $row['id']; ?>&type=3"
            class="tooltip-error deletelink" data-rel="tooltip" title=""> <span class="blue"> <i
                    class="ace-icon fa fa-print"></i> </span> </a>
    </td>
</tr>
<?php	
			}
			if(!empty($perpageresult)) 
			{
			?>
<input type="hidden" name="rowcount" id="rowcount" value="<?php echo $_POST["rowcount"]; ?>" />
<tr style="height: 44px;">
    <td colspan="6">
        <div style="margin-top: 10px;"><?php echo $perpageresult; ?></div>
    </td>
</tr>
<?php
			}
		}
		else
		{
			return $this->render('installments');
		}
	}
	
	public function actionInsprints()
	{
        $login_id = $_SESSION['user_array']['id'] ?? null;
        $id = $_REQUEST['id'] ?? null;
        $type = $_REQUEST['type'] ?? null;
    
        if (!$id) {
            throw new NotFoundHttpException("No transaction ID provided.");
        }
        if (!$type) {
            throw new NotFoundHttpException("No print type provided.");
        }
    
        if (!$login_id) {
            throw new NotFoundHttpException("Login session does not exist.");
        }
    
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("The requested transaction does not exist.");
        }
    
        // Logging activity
        $activity_type = '15'; // Journal Voucher
        
        // 	0=Installment Receiving,1=Bill,2=Salary,5=Receiving	
        $typee = "N/A";
        $url = Yii::$app->urlManager->baseUrl . "/index.php?r=transaction/ins_recieved";
        if($type==1)
        {
            $typee = "Journal Voucher";
            $url = Yii::$app->urlManager->baseUrl . "/Print/transaction.php?id=$id&mem=$login_id";
        }
        else if($type==2)
        {
            $typee = "Journal Voucher";
            $url = Yii::$app->urlManager->baseUrl . "/Print/transaction.php?id=$id&mem=$login_id";
        }
        else if($type==3)
        {
            $typee = "Journal Voucher";
            $url = Yii::$app->urlManager->baseUrl . "/Print/transaction_rec.php?id=$id&mem=$login_id";
        }
        
        else if($type==4)
        {
            $activity_type = '16';
            $url = Yii::$app->urlManager->baseUrl . "/Print/transaction.php?id=$id&mem=$login_id";
        }
        else if($type==5)
        {
            $activity_type = '16';
            $url = Yii::$app->urlManager->baseUrl . "/tcpdf/print/reciept_rudn.php?id=$id&mem=$login_id";
        }
        else if($type==6)
        {
            $activity_type = '18';
            if($model->status_type==10)
            {
                $activity_type = '17';
            }
            $url = Yii::$app->urlManager->baseUrl . "/tcpdf/print/transaction.php?id=$id&mem=$login_id";
        }
        else if($type==7)
        {
            $activity_type = '18';
            if($model->status_type==10)
            {
                $activity_type = '17';
            }
            $url = Yii::$app->urlManager->baseUrl . "/tcpdf/print/transaction.php?id=$id&mem=$login_id";
        }
        else if($type==8)
        {
            $activity_type = '18';
            if($model->status_type==10)
            {
                $activity_type = '17';
            }
            $url = Yii::$app->urlManager->baseUrl . "/tcpdf/print/payment_rudn.php?id=$id&mem=$login_id";
        }
        
        
        
        $action_type = 'Print';
        $transaction_id = $model->id;
        
        $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
        
        if(!$memberplot)
        {
            $memberplot['ref'] = "VoucherNo: ". $model->vno;
        }
        
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: $typee"
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
    
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
        
        return $this->redirect($url);
	    
	}

    public function actionUpdate_installment($id)
    {
        $model = $this->findModel($id);
        $connection = Yii::$app->getDb(); 
        if ($model->load(Yii::$app->request->post())) 
        { 
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            } 
            
            if($model->vtype==3){$model->bank_id=$_POST['ref'];}
            if($model->save())
            { 
                $sql = "Delete from payment WHERE vid='".$model->id."' AND pfor=1 ";
                \Yii::$app->db->createCommand($sql)->execute();

                $ref=$model->bank_id;
                $insert_id=$model->id;
                $balance=$model->amount;
                if(isset($_POST['tq']) && $_POST['tq'] > 0)
 				{ 
                        $q=1;
		                do  
                        {
                            if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && 
                            !empty($_POST['vid'.$q]))
						    {          
                                $comm1 = Commissionsub::find()->where(['pid' => $_POST['vid'.$q], 'status' => NULL])
                                ->all();           
                                foreach ($comm1 as $comm) 
                                { 
                                    if($comm->amount != '' || $comm->amount != 0)
                                    { 
                                        $com = Commission::find()->where(['id' => $comm->comid])->one(); 
                                        $sqltsa2 = "SELECT * from cconfig where type=17";
                                        $config = $connection->createCommand($sqltsa2)->queryOne();
              
                                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                        referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vid'.$q].", 
                                        pfor='10',amount='".$comm->amount."',date='".$model->create_date."',status='1',
                                        remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',
                                        type='0',jvid='0',gl='0',branch_id='".$model->branch_id."'";
                                        \Yii::$app->db->createCommand($sql)->execute();
             
                                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                        referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vid'.$q].", 
                                        pfor='10',amount1='".$comm->amount."',date='".$model->create_date."',status='1',
                                        remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',
                                        type='0',jvid='0',gl='0',branch_id='".$model->branch_id."'";
                                        \Yii::$app->db->createCommand($sql)->execute();
                  
                                        $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";
                                        \Yii::$app->db->createCommand($sql)->execute();
                                    }
                                }
                                $status=0;
                                if($_POST['status'.$q]==1){$status=1;}
                                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                referanceid='".$_POST['states-select']."',vid='".$insert_id."',sid=".$_POST['vid'.$q].", 
                                pfor='1',amount1='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',
                                remarks='".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='".$status."',
                                gl='0',branch_id='".$model->branch_id."'";
			        		    \Yii::$app->db->createCommand($sql)->execute();
                                $balance=$balance-$_POST['qamount'.$q];
							}							
							$q=$q+1;
						}while($q < ($_POST['tq']+1));	 
                }
                if($balance>0)
				{ 
                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',
                    vid=".$insert_id.", pfor='1',amount1='".($balance)."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();
				 	
				}  

                

                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                referanceid='".$ref."',vid=".$insert_id.", pfor='1',
                amount='".($model->amount)."',date='".$model->create_date."',status='1',
                remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                gl='0',branch_id='".$model->branch_id."'";
                \Yii::$app->db->createCommand($sql)->execute();
			
				// $a='Receive Installments';
    //             $at='Update';
    //             $u=$insert_id;
    //             $d = '';
    //             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                
                $a='15'; // Receive Installments ID in system_activities
                $at='Update';
                $u= $insert_id;
                
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$_POST['states-select']."' AND type=1 ")->queryOne();
                
                $details = "VoucherDate: ".$model->create_date.",ReceiptType: $voucher_type, Receipt#: ".$model->receipt.",
                            Amount: ".$model->amount.", Narration: ".$model->remarks." ";
                Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$memberplot['ref']);
            
            
                

            return 1; 
            }  
            else
            {
                return 0;
            }
        } 
        else
        { 
            return $this->render('update_installment', ['model' => $model]);
        }

    }

    public function actionIndex()
    {

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->orderBy(['id' => SORT_DESC,]);
        
        if($_SESSION["user_array"]["usertype"]==2)
		{
		$dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
		}
		$dataProvider->query->andFilterWhere(['transaction.status_type'=>5]);
		$dataProvider->query->andFilterWhere(['IN','transaction.vtype',[4,3]]);
		
		$dataProvider->pagination->pageSize=$request->get("pagesize",20);
        if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
		$dataProvider->pagination->page=($_REQUEST['page']-1);
		

        if($request->isAjax)
        {
            return $this->renderPartial('_index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else
        {
			$searchModel1='';
			$dataProvider1='';
            return $this->render('index', [
                'searchModel1' => $searchModel1,
                'dataProvider1' => $dataProvider1,
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionCreate_rec()
    {
        $model = new Transaction();
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            }
            $model->fc=0;
            $model->rate=0;

            $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
            and vtype='".$model->vtype."'";
            $result_max = $connection->createCommand($sql_max)->queryOne();
            $model->vno=($result_max['MAX(vno)']+1);
            
            if($model->vtype==3){$model->bank_id=$_POST['ref'];}
            if($model->save())
            { 
                $ref=$model->bank_id;
                $insert_id=$model->id;
                
				if($model->amount!==0)
                {
                    $sql_ac = "SELECT type from accounts WHERE id='".$model->pt_id."'";
                    $result_ac = $connection->createCommand($sql_ac)->queryOne();
                    $status=0;
                    if($result_ac['type']==1){$status=1;}
                    
                    
                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                    vid=".$insert_id.", pfor='1',amount='".($model->amount-$tt)."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();
 	
                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                    referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',
                    amount1='".($model->amount+$ta)."',date='".$model->create_date."',status='1',
                    remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='".$status."',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();
                }
                     

                $mobile=0;
                $ref='';$chk='';
                if($model->vtype==2 || $model->vtype==4)
                {
                    $ref='Ref # '.$model['vno'];
                    $chk='Cheque # '.$model['cheque_no'];
                }
                
                $sql_phone = "SELECT * , memberplot.id from accounts
                Left Join memberplot ON (memberplot.id=accounts.ref AND accounts.type=1)
                Left Join members ON (members.id=memberplot.member_id)
                where accounts.id='".$model->pt_id."'";
                $result_phone = $connection->createCommand($sql_phone)->queryOne();
                
                if(!empty($result_phone['phone']))
                {
                    $mb_array=array();
                    $mb_array = explode(",", $result_phone['phone']);
                            
                    for($i=0;$i<count($mb_array);$i++)
                    {
                        $mb_array_i=0;
                        if(strlen($mb_array[$i])>9 && strlen($mb_array[$i])<=15)
                        {
                            $mb_array_i=str_replace(["-", "–"], '', $mb_array[$i]);   
                            $mobile='+92'.(substr($mb_array_i, -10));
                            $message='Dear Member, 
Thank you for your payment of PKR '.number_format($model->amount,0).'/-   for Your MS # '.$result_phone['plotno'].' '.$ref.' '.$chk.'
Your ledger has been updated and can be verified online.
Contact Us at : 03041118888';
                            if($model->create_date==date('Y-m-d'))
                            {
                                // Yii::$app->mycomponent->Send_text_msg($mobile,$message);
                            }
                        }
                    }
                }  
			
				// $a='Receive Payment';
    //             $at='Create';
    //             $u=$model->id;
    //             $d = '';
    //             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                
                $a='16'; // Receive Installments ID in system_activities
                $at='Create';
                $u= $model->id;
                
                $details = "VoucherDate: ".$model->create_date.",ReceiptType: $voucher_type, Receipt#: ".$model->receipt.",
                            Amount: ".$model->amount.", Narration: ".$model->remarks." ";
                Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$result_phone['id']);
                

                return 1; 
            } 
            else
            { 
                return 0;
            }
        } 
        else
        {
            return $this->render('create_rec', ['model' => $model]);
        }       
    }

    public function actionUpdate_ins($id)
    {
        $model = $this->findModel($id);
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            } 
            
            if($model->vtype==3){$model->bank_id=$_POST['ref'];}
            if($model->save())
            {  
                $insert_id=$model->id;
                $ref=$model->bank_id; 
                
				if($model->amount!==0)
                {
                    $sql_ac = "SELECT type from accounts WHERE id='".$model->pt_id."'";
                    $result_ac = $connection->createCommand($sql_ac)->queryOne();
                    $status=0;
                    if($result_ac['type']==1){$status=1;}
       	                  
                    $sql = "UPDATE payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                    amount='".$model->amount."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."' WHERE vid=".$insert_id." AND pfor='1' AND amount>0";
                    \Yii::$app->db->createCommand($sql)->execute();
 	
                    $sql = "UPDATE payment SET salecenter='".$model->salecenter."',
                    referanceid='".$model->pt_id."', 
                    amount1='".$model->amount."',date='".$model->create_date."',status='1',
                    remarks='".$model->remarks."',type='0',jvid='".$status."',
                    gl='0',branch_id='".$model->branch_id."' WHERE vid=".$insert_id." AND pfor='1' AND amount1>0";
                    \Yii::$app->db->createCommand($sql)->execute();
                } 
			
				$a='Receive Payment';
                $at='Update';
                $u=$insert_id;
                $d='';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                
                $a='16'; // Receive Installments ID in system_activities
                $at='Update';
                $u= $model->id;
                
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
                $details = "VoucherDate: ".$model->create_date.",ReceiptType: Adjustment Voucher, Receipt#: ".$model->receipt.", Amount: ".$model->amount.", Narration: ".$model->remarks." ";
                Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$memberplot['ref']);
                
                return 1; 
            } 
            else
            { 
                return 0;
            }
        } 
        else
        {
            return $this->render('update_ins', ['model' => $model]);
        }       
    }

    public function actionIndex1()
    {

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['id' => SORT_DESC,]);
        if($_SESSION["user_array"]["usertype"]==2)
		{
		$dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
		}
		$dataProvider->query->andFilterWhere(['transaction.vtype'=>[1,2]]);
		$dataProvider->query->andFilterWhere(['transaction.status_type'=>[1,3,5,10]]);
        
        $dataProvider->pagination->pageSize=$request->get("pagesize",20);
         
         if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
		$dataProvider->pagination->page=($_REQUEST['page']-1);
		
		$request1 = Yii::$app->request;
		
		if($request->isAjax)
        {
            return $this->renderPartial('_index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else
        {
			$searchModel1='';
			$dataProvider1='';
            return $this->render('index1', [
                'searchModel1' => $searchModel1,
                'dataProvider1' => $dataProvider1,
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionCreate1()
    {
        $model = new Transaction();
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            }
            $model->fc=0;
            $model->rate=0;

            $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
            and vtype='".$model->vtype."'";
            $result_max = $connection->createCommand($sql_max)->queryOne();
            $model->vno=($result_max['MAX(vno)']+1);
            
            if($model->vtype==1){$model->bank_id=$_POST['ref'];}
            if($model->save())
            { 
                $ref=$model->bank_id;
                $insert_id=$model->id;
                
				if($model->amount!==0)
                {
                    $balance=$model->amount;
                    if(isset($_POST['totaltax']) && $_POST['totaltax'] > 0)
                    {
                        for($i=1;$i<=$_POST['totaltax'];$i++)
                        {
                            $tax  = "SELECT * from tax where tax_id='".$_POST['acctax'.$i]."'";
		                    $taxr = $connection->createCommand($tax)->queryOne();

                            $aid=0;

                            $remarks='';
                            if(!empty($_POST['narrationtax'.$i]))
                            {
                                $remarks=str_replace("'","",$_POST['narrationtax'.$i]);
                            }


                            if($taxr['aid']>0){$aid=$taxr['aid'];}

                            $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                            referanceid='".$aid."',vid=".$insert_id.", pfor='1',
                            amount='".$_POST['amounttax'.$i]."',date='".$model->create_date."',status='1',
                            remarks='".$remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                            gl='0',branch_id='".$model->branch_id."',ptyid='".$_POST['acctax'.$i]."'";
                            \Yii::$app->db->createCommand($sql)->execute();


                            $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                            referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',
                            amount1='".$_POST['amounttax'.$i]."',date='".$model->create_date."',status='1',
                            remarks='".$remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                            gl='0',branch_id='".$model->branch_id."',ptyid='".$_POST['acctax'.$i]."'";
                            \Yii::$app->db->createCommand($sql)->execute();

                            // $balance=$balance-$_POST['amounttax'.$i];

                        }
                    }
                    if(isset($_POST['tq']) && $_POST['tq'] > 0)
 				    { 
                        $q=1;
		                do  
                        {
                            if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && 
                            !empty($_POST['vid'.$q]))
						    {                                 
       	                   	
                                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',pro_id='".$_POST['vid'.$q]."',
                                amount='".($_POST['qamount'.$q])."',date='".$model->create_date."',status='1',
                                remarks='".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                                gl='0',branch_id='".$model->branch_id."'";
                                \Yii::$app->db->createCommand($sql)->execute();
                                $balance=$balance-$_POST['qamount'.$q];
                            }
                            $q=$q+1;
						}while($q < ($_POST['tq']+1));
                        

                    }      
                    if($balance>0)
                    { 
       	                   	
                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                        referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',
                        amount='".($balance)."',date='".$model->create_date."',status='1',
                        remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                        gl='0',branch_id='".$model->branch_id."'";
                        \Yii::$app->db->createCommand($sql)->execute(); 

                    }

                    

                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                    vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();


                }
                      
			
				// $a='Payment Voucher';
    //             $at='Create';
    //             $u=$insert_id;
    //             $d = '';
    //         Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                $activity_type = 18;
                $action_type = 'Create';
                $transaction_id = $model->id;
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
                
                if(!$memberplot)
                {
                    $memberplot['ref'] = 'VoucherNo: '.$model->vno;
                }
                $details = "VoucherDate: " . $model->create_date
                    . ", ReceiptType: " . $voucher_type
                    . ", Receipt#: " . $model->receipt
                    . ", Amount: " . $model->amount
                    . ", Narration: " . $model->remarks;
            
                Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
        
                

                return 1; 
            } 
            else
            { 
                return 0;
            }
        } 
        else
        {
            return $this->render('create_payment', ['model' => $model]);
        }       
    }

    public function actionUpdate1($id)
    {
        $model = $this->findModel($id);
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            } 
            
            if($model->vtype==1){$model->bank_id=$_POST['ref'];}
            if($model->save())
            {  
                $insert_id=$model->id;
                $ref=$model->bank_id; 
                

                $sql = "Delete  from payment where vid=".$insert_id." AND pfor='1' ";
                \Yii::$app->db->createCommand($sql)->execute();
                
				if($model->amount!==0)
                {
                    $balance=$model->amount;
                    if(isset($_POST['totaltax']) && $_POST['totaltax'] > 0)
                    {
                        for($i=1;$i<=$_POST['totaltax'];$i++)
                        {
                            $tax  = "SELECT * from tax where tax_id='".$_POST['acctax'.$i]."'";
		                    $taxr = $connection->createCommand($tax)->queryOne();

                            $aid=0;

                            
                            $remarks='';
                            if(!empty($_POST['narrationtax'.$i]))
                            {
                                $remarks=str_replace("'","",$_POST['narrationtax'.$i]);
                            }
                              


                            if($taxr['aid']>0){$aid=$taxr['aid'];}

                            $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                            referanceid='".$aid."',vid=".$insert_id.", pfor='1',
                            amount='".$_POST['amounttax'.$i]."',date='".$model->create_date."',status='1',
                            remarks='".$remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                            gl='0',branch_id='".$model->branch_id."',ptyid='".$_POST['acctax'.$i]."'";
                            \Yii::$app->db->createCommand($sql)->execute();
                            


                            $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                            referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',
                            amount1='".$_POST['amounttax'.$i]."',date='".$model->create_date."',status='1',
                            remarks='".$remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                            gl='0',branch_id='".$model->branch_id."',ptyid='".$_POST['acctax'.$i]."'";
                            \Yii::$app->db->createCommand($sql)->execute();
                            
                            // $balance=$balance-$_POST['amounttax'.$i];

                        }
                    }
                    if(isset($_POST['tq']) && $_POST['tq'] > 0)
 				    { 
                        $q=1;
		                do  
                        {
                            if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && 
                            !empty($_POST['vid'.$q]))
						    {                                 
       	                   	
                                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                                referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',pro_id='".$_POST['vid'.$q]."',
                                amount='".($_POST['qamount'.$q])."',date='".$model->create_date."',status='1',
                                remarks='".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                                gl='0',branch_id='".$model->branch_id."'";
                                \Yii::$app->db->createCommand($sql)->execute();
                                $balance=$balance-$_POST['qamount'.$q];
                            }
                            $q=$q+1;
						}while($q < ($_POST['tq']+1));
                        

                    }      
                    if($balance>0)
                    { 
       	                   	
                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                        referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',
                        amount='".($balance)."',date='".$model->create_date."',status='1',
                        remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                        gl='0',branch_id='".$model->branch_id."'";
                        \Yii::$app->db->createCommand($sql)->execute(); 

                    } 
                    
                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                    vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();
                }
			
				// $a='Payment Voucher';
    //             $at='Update';
    //             $u=$insert_id;
    //             $d = '';
    //             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                $activity_type = 18;
                $action_type = 'Update';
                $transaction_id = $model->id;
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
                
                if(!$memberplot)
                {
                    $memberplot['ref'] = 'VoucherNo: '.$model->vno;
                }
                $details = "VoucherDate: " . $model->create_date
                    . ", ReceiptType: " . $voucher_type
                    . ", Receipt#: " . $model->receipt
                    . ", Amount: " . $model->amount
                    . ", Narration: " . $model->remarks;
            
                Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
        
                return 1; 
            } 
            else
            {  
                return 0;
            }
        } 
        else
        {
            return $this->render('update_payment', ['model' => $model]);
        }       
    }

    public function actionCreate1_expense()
    {
        $model = new Transaction();
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            }
            $model->fc=0;
            $model->rate=0;
            $model->pt_id=0;

            $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
            and vtype='".$model->vtype."'";
            $result_max = $connection->createCommand($sql_max)->queryOne();
            $model->vno=($result_max['MAX(vno)']+1);
            
            if($model->vtype==1){$model->bank_id=$_POST['ref'];}
            if($model->save())
            { 
                $ref=$model->bank_id;
                $insert_id=$model->id;

                if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0)
                {
                    for($i=1;$i<=$_POST['totalexp'];$i++)
                    {
                        
                        $remarks='';
                        if(!empty($_POST['narration'.$i]))
                        {
                            $remarks=str_replace("'","",$_POST['narration'.$i]);
                        }
                          
                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                        referanceid='".$_POST['acc'.$i]."',vid=".$insert_id.", pfor='1',
                        amount='".$_POST['amount'.$i]."',date='".$model->create_date."',status='1',
                        remarks='".$remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                        gl='0',branch_id='".$model->branch_id."',cost_center='".$_POST['cost_center'.$i]."'";
                        \Yii::$app->db->createCommand($sql)->execute();

                    }
                }
				 

                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',
                status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                gl='0',branch_id='".$model->branch_id."'";
                \Yii::$app->db->createCommand($sql)->execute();
                 
                      
			
				$a='Expense';
                $at='Create';
                $u=$insert_id;
                $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                

                return 1; 
            } 
            else
            { 
                return 0;
            }
        } 
        else
        {
            return $this->render('create1_expense', ['model' => $model]);
        }       
    }

    public function actionUpdate1ex($id)
    {
        $model = $this->findModel($id);
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            } 
            
            if($model->vtype==1){$model->bank_id=$_POST['ref'];}
            if($model->save())
            { 
                $ref=$model->bank_id;
                $insert_id=$model->id;

                $sql = "Delete  from payment where vid=".$insert_id." AND pfor='1' ";
                \Yii::$app->db->createCommand($sql)->execute();

                if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0)
                {
                    for($i=1;$i<=$_POST['totalexp'];$i++)
                    {
                        
                        $remarks='';
                        if(!empty($_POST['narration'.$i]))
                        {
                            $remarks=str_replace("'","",$_POST['narration'.$i]);
                        }
                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                        referanceid='".$_POST['acc'.$i]."',vid=".$insert_id.", pfor='1',
                        amount='".$_POST['amount'.$i]."',date='".$model->create_date."',status='1',
                        remarks='".$remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                        gl='0',branch_id='".$model->branch_id."',cost_center='".$_POST['cost_center'.$i]."'";
                        \Yii::$app->db->createCommand($sql)->execute();

                    }
                }
				 

                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',
                status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                gl='0',branch_id='".$model->branch_id."'";
                \Yii::$app->db->createCommand($sql)->execute();
                 
                      
			
				$a='Expense';
                $at='Update';
                $u=$insert_id;
                $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                

                return 1; 
            } 
            else
            { 
                return 0;
            }
        } 
        else
        {
            return $this->render('update1ex', ['model' => $model]);
        }       
    }

    public function actionRefund()
    {
        $model = new Transaction();
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            }
            $model->fc=0;
            $model->rate=0;

            $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
            and vtype='".$model->vtype."'";
            $result_max = $connection->createCommand($sql_max)->queryOne();
            $model->vno=($result_max['MAX(vno)']+1);
            
            if($model->vtype==1){$model->bank_id=$_POST['ref'];}
            if($model->save())
            { 
                $ref=$model->bank_id;
                $insert_id=$model->id;
                
				if($model->amount!==0)
                {
       	                   	
                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                    referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',
                    amount='".($model->amount)."',date='".$model->create_date."',status='1',
                    remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();

                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                    vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();
                }
                      
			
				// $a='Refund';
    //             $at='Create';
    //             $u=$insert_id;
    //             $d = '';
    //             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                
                $activity_type = 17; // For Refund
                $action_type = 'Create';
                $transaction_id = $model->id;
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
                
                if(!$memberplot)
                {
                    $memberplot['ref'] = 'VoucherNo: '.$model->vno;
                }
                $details = "VoucherDate: " . $model->create_date
                    . ", ReceiptType: " . $voucher_type
                    . ", Receipt#: " . $model->receipt
                    . ", Amount: " . $model->amount
                    . ", Narration: " . $model->remarks;
            
                Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
        
                

                return 1; 
            } 
            else
            { 
                return 0;
            }
        } 
        else
        {
            return $this->render('refund', ['model' => $model]);
        }       
    }

    public function actionUpdate_refund($id)
    {
        $model = $this->findModel($id);
        
        $connection = Yii::$app->getDb();
        if ($model->load(Yii::$app->request->post())) 
        {
            $model->create_date = date("Y-m-d", strtotime($model->create_date));
            if(empty($model->cheque_date))
            {
                $model->cheque_date = NULL;
            }
            else
            { 
                $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
            } 
            
            if($model->vtype==1){$model->bank_id=$_POST['ref'];}
            if($model->save())
            { 
                $ref=$model->bank_id;
                $insert_id=$model->id;

                $sql = "Delete  from payment where vid=".$insert_id." AND pfor='1' ";
                \Yii::$app->db->createCommand($sql)->execute();
                
				if($model->amount!==0)
                {
       	                   	
                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                    referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',
                    amount='".($model->amount)."',date='".$model->create_date."',status='1',
                    remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();

                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',
                    vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',
                    status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',
                    gl='0',branch_id='".$model->branch_id."'";
                    \Yii::$app->db->createCommand($sql)->execute();
                }
                      
			
				// $a='Refund';
    //             $at='Update';
    //             $u=$insert_id;
    //             $d = '';
    //             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                
                
                $activity_type = 17; // For Refund
                $action_type = 'Update';
                $transaction_id = $model->id;
                $voucher_type = "N/A";
                if($model->vtype==3){
                    $voucher_type = 'Cash Reciept';
                }
                else if($model->vtype==4)
                {
                    $voucher_type = 'Bank Reciept';
                }
            
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
                
                if(!$memberplot)
                {
                    $memberplot['ref'] = 'VoucherNo: '.$model->vno;
                }
                $details = "VoucherDate: " . $model->create_date
                    . ", ReceiptType: " . $voucher_type
                    . ", Receipt#: " . $model->receipt
                    . ", Amount: " . $model->amount
                    . ", Narration: " . $model->remarks;
            
                Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);

                return 1; 
            } 
            else
            { 
                return 0;
            }
        } 
        else
        {
            return $this->render('update_refund', ['model' => $model]);
        }       
    }

    public function actionReceiptc()
    {
                return $this->render('transection');
    }
	public function actionReceiptB()
    {
                return $this->render('transection');
    }
    
    public function actionCheck_dup_new()
    {
	    $con = Yii::$app->getDb();
        $sql = "SELECT * FROM transaction WHERE receipt = '". $_REQUEST['receipt_no'] ."'";
        $res = $con->createCommand($sql)->queryAll();
        
        if ( !empty ( $res ) )
        {
        ?>
<div class="row">
    <div class="col-sm-12">
        <h4 class="center red">These vouchers are already generated with same receipt number. Do you want to duplicate
            it?</h4>
    </div>
</div>
<table id="simple-table" class="table  table-bordered table-hover">
    <thead>
        <tr>
            <th>Sr #</th>
            <th>Receipt No.</th>
            <th>Type</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <?php
                    $counter = 1;
                    foreach ( $res as $row )
                    {
                        $type = '';
                        if ( $row['vtype'] == 1 ) { $type = 'CPV'; }
                        if ( $row['vtype'] == 2 ) { $type = 'BPV'; }
                        if ( $row['vtype'] == 3 ) { $type = 'CRV'; }
                        if ( $row['vtype'] == 4 ) { $type = 'BRV'; }
                        if ( $row['vtype'] == 5 || $row['vtype'] == 7 ) { $type = 'JV'; }
                    ?>
        <tr>
            <td><?php echo $counter; ?></td>
            <td><?php echo $row['receipt']; ?></td>
            <td><?php echo $type; ?></td>
            <td><?php echo date('d-m-Y', strtotime($row['create_date'])); ?></td>
            <td><?php echo number_format($row['amount']); ?></td>
            <td><?php echo $row['remarks']; ?></td>
        </tr>
        <?php
                        $counter++;
                    }
                    ?>
    </tbody>
</table>
<div class="row">
    <div class="col-sm-12">
        <button type="button" class="btn btn-sm btn-success pull-right" onclick="form_submit1();"
            style="border-radius: 4px;">Yes Duplicate it!</button>
    </div>
</div>
<?php
        // return 1;
            exit;
        }
        else
        {
             echo 'success';
            exit;
        }
    }
    
    public function actionCheck_dup()
	{
	    $connection = Yii::$app->getDb();
	    $where='';
	    if(isset($_REQUEST['id']) && $_REQUEST['id']>0)
	    {
	        $where=" AND id!='".$_REQUEST['id']."'";
	    }
        $current_year='';
        $current_year = date('Y', strtotime($_REQUEST['date']));
        if ( date ( 'm', strtotime ( $_REQUEST['date'] ) ) < 7 )
        {
            $current_year= $current_year-1;
        }
        
        $fiscal_year_start_date = $current_year . "-06-30";
        
        // $am  = "SELECT * from transaction where receipt='".$_REQUEST['value']."' $where";
		
		$am  = "SELECT * from transaction where receipt='".$_REQUEST['value']."'
		AND transaction.create_date > '". $fiscal_year_start_date ."'
        AND transaction.create_date <= DATE_ADD('". $fiscal_year_start_date ."', INTERVAL 1 YEAR) $where";
		$amr = $connection->createCommand($am)->queryOne();
		if ( $amr['id'] > 0 ) { return 1; } else { return 0; }
	}
	public function actionCheck_dup1()
	{
	    exit;
	    $connection = Yii::$app->getDb();			
			
		$am  = "SELECT * from transaction where cheque_no='".$_REQUEST['value']."'";
		$amr = $connection->createCommand($am)->queryOne();
		if($amr['id']>0)
		{
		    echo 1;
		}
	}
    
    public function actionGet_salary()
	{
	    $connection = Yii::$app->getDb();			
			
		$am  = "SELECT *,accounts.id as aid from accounts 
		left join employee on (employee.id=accounts.ref)
		where type=4 and employee.id is NOT NULL";
		$amr = $connection->createCommand($am)->queryAll();	
		
		$i=0;$j=0;
		foreach ($amr as $row)
		{
		    $am1  = "SELECT SUM(amount),SUM(amount1) from payment
    		where referanceid='".$row['aid']."'";
    		$amr1 = $connection->createCommand($am1)->queryOne();	
    		$rem=0;
    		$rem=$amr1['SUM(amount)']-$amr1['SUM(amount1)'];
    		
    		
		    $i++;
		    ?>
<tr>
    <td><?php echo $row['name']; ?>
        <input type="hidden" name="eid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $row['aid']?>" />
    </td>
    <td><?php echo  number_format($rem,2) ?></td>
    <td>
        <input type="text" style="width:100px;height: 20px;" onblur="tamount()" name="qamount<?php echo $i;?>"
            id="qamount<?php echo $i;?>" value="0" />
    </td>
</tr>
<?php
    		
		    
		}
		?>
<tr style="display: none;">
    <td>
        <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
    </td>
</tr>
<?php
	    
	}
    public function actionSearchac()
	{
       		$connection = Yii::$app->getDb();
	
		if($_REQUEST['typeid']==1){
			$am  = "SELECT * from supplier";
			$amr = $connection->createCommand($am)->queryAll();	
			foreach ($amr as $row){
  ?>
<option value="<?php echo  $row['id']?>"><?php echo  $row['company_name']?></option>
<?php 
}	}
	if($_REQUEST['typeid']==2){
			$am  = "SELECT * from customer";
			$amr = $connection->createCommand($am)->queryAll();	
			foreach ($amr as $row){
  ?>
<option value="<?php echo  $row['id']?>"><?php echo  $row['name']?>-<?php echo  $row['ccode']?></option>

<?php 
}	}
    	if($_REQUEST['typeid']==3){
	$connection = Yii::$app->getDb();
			$am  = "SELECT * from subaccount";
			$amr = $connection->createCommand($am)->queryAll();	
			foreach ($amr as $row){
  ?>
<option value="<?php echo  $row['id']?>"><?php echo  $row['name']?>-<?php echo  $row['code']?></option>
<?php 
}	}}
	public function actionCpayment()
   	{
       			$model = new Transaction();
                return $this->render('transection', ['model' => $model]);
     }
	public function actionBpayment()
    {
                return $this->render('transection');
    }
	public function actionWord()
	{
$num = $_REQUEST['numm'];
    $num    = ( string ) ( ( int ) $num );

   

    if( ( int ) ( $num ) && ctype_digit( $num ) )

    {

        $words  = array( );

       

        $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );

       

        $list1  = array('','one','two','three','four','five','six','seven',

            'eight','nine','ten','eleven','twelve','thirteen','fourteen',

            'fifteen','sixteen','seventeen','eighteen','nineteen');

       

        $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',

            'seventy','eighty','ninety','hundred');

       

        $list3  = array('','thousand','million','billion','trillion',

            'quadrillion','quintillion','sextillion','septillion',

            'octillion','nonillion','decillion','undecillion',

            'duodecillion','tredecillion','quattuordecillion',

            'quindecillion','sexdecillion','septendecillion',

            'octodecillion','novemdecillion','vigintillion');

       

        $num_length = strlen( $num );

        $levels = ( int ) ( ( $num_length + 2 ) / 3 );

        $max_length = $levels * 3;

        $num    = substr( '00'.$num , -$max_length );

        $num_levels = str_split( $num , 3 );

       

        foreach( $num_levels as $num_part )

        {

			$levels--;
            $hundreds   = ( int ) ( $num_part / 100 );
            $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
            $tens       = ( int ) ( $num_part % 100 );
            $singles    = '';
            if( $tens < 20 )
            {
                $tens   = ( $tens ? ' ' . $list1[$tens] . ' ' : '' );
            }
            else
            { 
                $tens   = ( int ) ( $tens / 10 );
                $tens   = ' ' . $list2[$tens] . ' ';
                $singles    = ( int ) ( $num_part % 10 );
                $singles    = ' ' . $list1[$singles] . ' ';
            }
            $words[]    = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        }
        $commas = count( $words );
        if( $commas > 1 )
        {
            $commas = $commas - 1;
        }
        $words  = implode( ' ' , $words );
   return ucwords($words).' Only';

    }

    else if( ! ( ( int ) $num ) )

    {

        return 'Zero';

    }

    return '';}
		
	 
		
	
    public function actionSearchch1_all()
	{
       		$connection = Yii::$app->getDb();
			$q = $_POST['data']['q'];
			$am  = "SELECT *,plot_reserved_main.id as aid,members.name as mname from plot_reserved_main
			Left Join members ON (members.id=plot_reserved_main.member_id)
			where rno Like '%".$q."%' OR members.name Like '%".$q."%'"; 
			$amr = $connection->createCommand($am)->queryAll();	
			$return='';
		
                foreach ($amr as $row)
                {
                    $t='';
                 
 			        $results[] = array('id' => $row['aid'], 'text' => $row['rno'].' - '.$row['mname']);
			}	
			echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
    	}
    
    public function actionSearchchdealer()
    {
          $connection = Yii::$app->getDb();
          if(isset($_REQUEST['chek']) && $_REQUEST['chek']==1){
             
			$q = $_POST['data']['q'];
			$am  = "SELECT *,plot_reserved_main.id as aid,members.name as mname from plot_reserved_main
			Left Join members ON (members.id=plot_reserved_main.member_id)
			where rno Like '%".$q."%' OR members.name Like '%".$q."%'"; 
			$amr = $connection->createCommand($am)->queryAll();	
			$return='';
		
                foreach ($amr as $row)
                {
                    $t='';
                 
 			        $results[] = array('id' => $row['aid'], 'text' => $row['rno'].' - '.$row['mname']);
			}	
			echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
    
              
          }else{
      $q = $_POST['data']['q'];
      $am  = "SELECT *,accounts.id as aid  from accounts   where (accounts.name Like '%".$q."%') AND accounts.type in  (2,3,4,5)";
      $amr = $connection->createCommand($am)->queryAll(); 
      $return='';
      foreach ($amr as $row){$t='';
                
      $results[] = array('id' => $row['aid'], 'text' =>  $row['name']);
      } 
      echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
          }
              
          }
    
    public function actionSearchch_salary()
	{
       		$connection = Yii::$app->getDb();
			$q = $_POST['data']['q'];
			$am  = "SELECT * from accounts where type=4 AND name Like '%".$q."%'";
			$amr = $connection->createCommand($am)->queryAll();	
			$return='';
		
                foreach ($amr as $row){$t='';
                    if($row['type']==1){$t='CC-';}
                    if($row['type']==2){$t='SS-';}
 			$results[] = array('id' => $row['id'], 'text' => $t.$row['code'].' - '.$row['name']);
			}	
			echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
    	}
    	
     
	public function actionTaxfind()
	{
       		$connection = Yii::$app->getDb();
			$am  = "SELECT * from taxdefination 
			where id=".$_REQUEST['id']."";
			$amr = $connection->createCommand($am)->queryOne();	
			echo $amr['value'];
		}
	public function actionGetbalance()
	{
       		$connection = Yii::$app->getDb();
			$am  = "SELECT SUM(amount),SUM(amount1) from payment 
			where referanceid=".$_POST['value']."";
			$amr = $connection->createCommand($am)->queryOne();	
			
			$am1  = "SELECT * from accounts 
			where id=".$_POST['value']."";
			$am1r = $connection->createCommand($am1)->queryOne();	
			
				echo $bal=$amr['SUM(amount)']-$amr['SUM(amount1)'];
			
		}
    public function actionInstall()
    {
        $connection = Yii::$app->getDb();
        $am  = "SELECT * from payment where referanceid=".$_REQUEST['acc']." AND pfor = 2";
        $amr = $connection->createCommand($am)->queryAll(); 
        echo '<option value="">Select</option>';
        foreach ($amr as $am) 
        {
            $am1  = "SELECT SUM(amount1) from payment where sid=".$am['id']." ";
            $amr1 = $connection->createCommand($am1)->queryOne(); 
            
            if($amr1['SUM(amount1)']<$am['amount'])
            {
                echo '<option value="'.$am['id'].'">'.$am['remarks'].'</option>';
            }
        }
      
    }
    public function actionInstall1()
    {
        $connection = Yii::$app->getDb();
        $am  = "SELECT * from accounts where id=".$_REQUEST['acc']." AND type=1";
        $amr = $connection->createCommand($am)->queryOne();
        if($amr['id']>0){return 1;}else{return 0;}
    }
    public function actionInstallpay()
    {
        $connection = Yii::$app->getDb();
        $am  = "SELECT * from payment where id=".$_REQUEST['id']." AND pfor = 2";
        $amr = $connection->createCommand($am)->queryOne(); 
        
        if ( $amr['remarks'] == 'Receivable Amount' )
        {
            $am1  = "SELECT SUM(amount1) FROM payment WHERE pfor IN (1, 6) AND referanceid = '". $amr['referanceid'] ."' AND jvid > 0 ";
            $amr1 = $connection->createCommand($am1)->queryOne();
            
            echo ($amr['amount']-$amr1['SUM(amount1)']); exit;
        }
        else
        {
            $am1  = "SELECT SUM(amount1) from payment where sid=".$amr['id']." ";
            $amr1 = $connection->createCommand($am1)->queryOne();
            echo ($amr['amount']-$amr1['SUM(amount1)']); exit; 
        }
    }

    /**
     * Displays printable version of summary Transaction model.
     * @return mixed
     */
   
    public function actionPrintsummary()
    {
        $this->layout = "@app/views/layouts/print";

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		    $dataProvider->pagination->pageSize=$request->get("pagesize",2000);
        print_r($dataProvider);
        return $this->render('printsummary',[
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays PDF version of summary Transaction model.
     * @return mixed
     */
    public function actionPdfsummary()
    {
        $this->layout = "@app/views/layouts/print";

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $pdf = new Pdf([
                'content'=>$this->render('printsummary',[
                                'dataProvider' => $dataProvider,
                                ]),
                'filename'=> "report.pdf",
                'mode'=> Pdf::MODE_CORE,
                'format'=> Pdf::FORMAT_A4,
                'destination'=> Pdf::DEST_DOWNLOAD
                ]);
        return $pdf->render();
    }

    /**
     * Displays a single Transaction model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        $request = Yii::$app->request;

        if($request->isAjax)
        {
            return $this->renderPartial('_view', [
                'model' => $this->findModel($id),
            ]);
        }
        else
        {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Transaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
	 public function actionCinfo()
	{
		$connection = Yii::$app->getDb();

    $am  = "SELECT type from accounts where id='".$_REQUEST['mid']."'";
        $amr = $connection->createCommand($am)->queryOne(); 
    if($amr['type'] == 1){
    $amas  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
    $amras = $connection->createCommand($amas)->queryOne(); 
    $am  = "SELECT * from memberplot 
    left join members ON(memberplot.member_id=members.id)
    where memberplot.id='".$amras['ref']."'";
    $amr = $connection->createCommand($am)->queryOne();  
    if(!empty($amr['name']))
    {
      echo '<p> <b>Name</b> : '.$amr['name'].'<br><b>Address</b> : '.$amr['address'].'<br><b>Mobile #</b> : '.$amr['mobile'].'</p>';
    }
  } else {
    $am  = "SELECT *,customer.contact_name as cname from supplier
    Left Join accounts ON(accounts.ref=supplier.id AND accounts.type=2)
    where accounts.id='".$_REQUEST['mid']."'";
    $amr = $connection->createCommand($am)->queryOne();  
    if(!empty($amr['cname']))
    {
      echo '<p> <b>Name</b> : '.$amr['cname'].'<br><b>Address</b> : '.$amr['address'].'<br><b>Contact #</b> : '.$amr['contactno'].'</p>';
    }
  }
	}
	 public function actionSearchch_emp()
	    {
       		$connection = Yii::$app->getDb();
			$q = $_POST['data']['q'];
			$am  = "SELECT * from accounts where name Like '%".$q."%' AND type=4"; 
			$amr = $connection->createCommand($am)->queryAll();	
			$return='';
		
                foreach ($amr as $row)
                {
                    $t='';
 			        $results[] = array('id' => $row['id'], 'text' => $t.$row['code'].' - '.$row['name']);
			}	
			echo json_encode(array('q' => $_POST['data']['q'], 'results' => $results));
    	}
	 
       
    public function actionPaid()
    {
      
        $connection = Yii::$app->getDb();
    
        $paid = "SELECT SUM(amount1) from payment where referanceid='".$_REQUEST['id']."'";
        $paidr = $connection->createCommand($paid)->queryOne();
        if($paidr['SUM(amount1)']>0)
        {
            echo $paidr['SUM(amount1)'];
        }
        else
        {
            echo 0;
        }
    
    }
    
    public function actionPayable()
    {
      
        $connection = Yii::$app->getDb();
    
        $paid = "SELECT SUM(amount) from payment where referanceid='".$_REQUEST['id']."'";
        $paidr = $connection->createCommand($paid)->queryOne();
        if($paidr['SUM(amount)']>0)
        {
            echo $paidr['SUM(amount)'];
        }
        else
        {
            echo 0;
        }
    
    }
    	
  public function actionSalesp1()
  {
    
          $connection = Yii::$app->getDb(); 
          if(isset($_REQUEST['val'])){
            $and = 'AND payment.id = "'.$_REQUEST['val'].'"';
          } else {
            $and = '';
          }

          $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
          $acc1 = $connection->createCommand($acc)->queryOne(); 
        

       $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' and  vid = '".$acc1['ref']."' $and ORDER BY `date`";
      
      $amr = $connection->createCommand($am)->queryAll();     
        $i=0;$j=0;
        $arr = array();

        foreach ($amr as $row)
        {
          
          
        $am1  = "SELECT * from memberplot where id='".$row['vid']."'";
          $amr1 = $connection->createCommand($am1)->queryOne(); 
        
    //echo  $am2  = "SELECT SUM(amount) from payment where referanceid='".$_REQUEST['mid']."'  AND vid='".$row['vid']."' AND (pfor=2 or pfor = '')";
         // $amr2= $connection->createCommand($am2)->queryOne();
// AND pro_id = '".$row['vid']."'

$wherev='';
		if(isset($_REQUEST['vid']) && $_REQUEST['vid']>0)
		{$wherev=" AND vid!='".$_REQUEST['vid']."'";}	

          $amm  = "SELECT SUM(amount1) as am from payment where pfor IN (1,6) AND referanceid='".$_REQUEST['mid']."' and sid='".$row['id']."' $wherev";

          $amrm = $connection->createCommand($amm)->queryOne();
          
          
        //  $r=$amr2['SUM(amount1)']-$amr3['SUM(amount)'];
          $r = $row['amount'] - $amrm['am'];
          if($r!=0)
          { $temp = array();
              $i=$i+1;
              
                $status=0;
                if($row['remarks']=='Receivable Amount'){$row['remarks']="Receivable Against Plot/File";$status=1;}
            ?>
<tr>
    <td><?php $a = $i.':- '.$amr1['plotno']; echo $i.':- '.$amr1['plotno']; 

              array_push($temp, $a);
               array_push($temp, $row['id']);

               array_push($arr, $temp);

              ?>

        <input type="hidden" name="vids<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $row['id']?>" />
    </td>
    <td><?php echo $row['remarks'];?>
        <input type="hidden" name="rems<?php echo $i;?>" id="rems<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $row['remarks']?>" />
    </td>
    <td><?php echo  number_format($r,2) ?>
        <input type="hidden" name="vs<?php echo $i;?>" id="vs<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $r?>" />

        <input type="hidden" name="status<?php echo $i;?>" id="status<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $status;?>" />
    </td>
    <td>
        <input type="text" style="border: 0px;border-bottom: 1px dotted;width:100px;height: 20px;"
            onblur="tamount('<?php echo $i;?>')" name="qamounts<?php echo $i;?>" id="qamounts<?php echo $i;?>" value="0"
            readonly="readonly" />
    </td>
</tr>
<?php }   
        }

         /*AND pfor=1 AND sid IS NULL*/
          $am4  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' AND amount1 != '' AND (sid IS NULL OR sid=0) AND pfor = 1";
          $amr4= $connection->createCommand($am4)->queryOne();
          if($amr4['amount1'] > 0)
          {
            
          $q  = "SELECT Distinct(vid),memberplot.id as qid,plotno from payment
          Join memberplot on (payment.vid=memberplot.id) 
          where pfor=2";
          $qr = $connection->createCommand($q)->queryAll(); 
          $j=$j+1;
       //   print_r($arr);
        //  exit();

           ?>

<?php  }
        
        ?>
<tr style="display: none;">
    <td>
        <input type="hidden" style="width:100px;height: 20px;" name="tqs" id="tqs" value="<?php echo $i;?>" />
    </td>
</tr>
<?php
      
  }

public function actionPursp()
	{
	    if(isset($_REQUEST['vv']) and $_REQUEST['vv']==1){
	        
	        $connection = Yii::$app->getDb();	
        $acc  = "SELECT * from plot_reserved_main where id='".$_REQUEST['mid']."'";
        $acc1 = $connection->createCommand($acc)->queryOne(); 
      //  print_r($acc1);exit;
        $i=1;
        $am  = "SELECT *,payment.id as pid from payment left JOIN transaction ON (transaction.id = payment.vid) where payment.vid='".$acc1['trans_id']."' and payment.pfor=1  AND payment.remarks != 'Tax Paid'";
        $amr = $connection->createCommand($am)->queryAll(); 
        $s=0;
        foreach ($amr as $amr1)
        {
            $wherev='';
		if(isset($_REQUEST['vid']) && $_REQUEST['vid']>0)
		{$wherev=" AND vid!='".$_REQUEST['vid']."'";}
            
            $amm  = "SELECT sum(amount) as sm from payment where pfor=1 AND pro_id = '".$amr1['pid']."' $wherev";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $amr1['amount1'] - $amrm['sm']; 
			
			if($r!=0 && $s<20)
			{ $s=$s+1;
			?>
<tr>
    <td>
        <?php 
			        if($amr1['pfor'] == 3)
			        {
                        echo $amr1['order_no'];
                    } 
                    if($amr1['pfor'] == 2)
                    {
                        $sqlmm= "SELECT * FROM memberplot where id='".$amr1['vid']."'";
                        $resultmm = $connection->createCommand($sqlmm)->queryOne();
                        echo $resultmm['plotno'];
                    };
                    if($amr1['pfor'] == 1)
                    {
                        $sqlmm= "SELECT * FROM transaction where id='".$amr1['vid']."'";
                        $resultmm = $connection->createCommand($sqlmm)->queryOne();
                        echo $resultmm['vno'];
                    };
                    ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $amr1['pid']?>" />
    </td>
    <td><?php echo $amr1['remarks'];?></td>
    <td><?php echo  number_format($r,2) ;?>
        <input type="hidden" name="rowid<?php echo $i;?>" id="rowid<?php echo $i;?>" value="<?php echo $amr1['pid'];?>">
        <input type="hidden" name="oam<?php echo $i;?>" id="oam<?php echo $i;?>" value="<?php echo $r;?>">
        <input type="hidden" name="rem<?php echo $i;?>" id="rem<?php echo $i;?>" value="<?php echo $amr1['remarks'];?>">
    </td>
    <td>
        <input type="hidden" name="v<?php echo $i;?>" id="v<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $r?>" />
        <input type="text" style="width:100px;height: 20px;" onblur="tamount(<?php echo $i ?>)"
            name="qamount<?php echo $i;?>" id="qamount<?php echo $i;?>" value="0" />
    </td>
</tr>
<?php 
				$i=$i+1;
			} 	
		}
		?>
<tr style="display: none;">
    <td>
        <input type="text" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
        <input type="text" style="width:100px;height: 20px;" name="expid" id="expid" value="<?php echo $i;?>" />
    </td>
</tr>

<?php
		exit;
	    }
	    $connection = Yii::$app->getDb();	
        $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
        $acc1 = $connection->createCommand($acc)->queryOne(); 
        $i=1;
        if(isset($_REQUEST['tq']) && $_REQUEST['tq']>0)
        {
            $i = $_REQUEST['tq']+1;
        }
        $am  = "SELECT * from payment left JOIN trans ON (trans.trans_id = payment.vid and pfor=3) where referanceid='".$_REQUEST['mid']."'  AND remarks != 'Tax Paid'";
        $amr = $connection->createCommand($am)->queryAll(); 
        $s=0;
        foreach ($amr as $amr1)
        {
            $amm  = "SELECT sum(amount) as sm from payment where referanceid='".$_REQUEST['mid']."' AND pfor=1 AND pro_id = '".$amr1['id']."'";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $amr1['amount1'] - $amrm['sm']; 
			
			if($r!=0 && $s<20)
			{ $s=$s+1;
			?>
<tr>
    <td>
        <?php 
			        if($amr1['pfor'] == 3)
			        {
                        echo $amr1['order_no'];
                    } 
                    if($amr1['pfor'] == 2)
                    {
                        $sqlmm= "SELECT * FROM memberplot where id='".$amr1['vid']."'";
                        $resultmm = $connection->createCommand($sqlmm)->queryOne();
                        echo $resultmm['plotno'];
                    };
                    if($amr1['pfor'] == 1)
                    {
                        $sqlmm= "SELECT * FROM transaction where id='".$amr1['vid']."'";
                        $resultmm = $connection->createCommand($sqlmm)->queryOne();
                        echo $resultmm['vno'];
                    };
                    ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $amr1['id']?>" />
    </td>
    <td><?php echo $amr1['remarks'];?></td>
    <td><?php echo  number_format($r,2) ;?>
        <input type="hidden" name="rowid<?php echo $i;?>" id="rowid<?php echo $i;?>" value="<?php echo $amr1['id'];?>">
        <input type="hidden" name="oam<?php echo $i;?>" id="oam<?php echo $i;?>" value="<?php echo $r;?>">
        <input type="hidden" name="rem<?php echo $i;?>" id="rem<?php echo $i;?>" value="<?php echo $amr1['remarks'];?>">
    </td>
    <td>
        <input type="hidden" name="v<?php echo $i;?>" id="v<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $r?>" />
        <input type="text" style="width:100px;height: 20px;" onblur="tamount(<?php echo $i ?>)"
            name="qamount<?php echo $i;?>" id="qamount<?php echo $i;?>" value="0" />
    </td>
</tr>
<?php 
				$i=$i+1;
			} 	
		}
		?>
<tr style="display: none;">
    <td>
        <input type="text" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
        <input type="text" style="width:100px;height: 20px;" name="expid" id="expid" value="<?php echo $i;?>" />
    </td>
</tr>
<?php
                    $am4  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' AND pfor=1 AND (pro_id IS NULL OR pro_id = 0) AND remarks != 'Tax Paid'";
			 		$amr4= $connection->createCommand($am4)->queryOne();
			 		if($amr4['amount'] > 0)
			 		{
					    $q  = "SELECT payment.remarks,payment.id,COALESCE(trans.order_no,memberplot.plotno) from payment 
					    left JOIN trans ON (trans.trans_id = payment.vid and pfor=3) 
					    left JOIN memberplot ON (memberplot.id = payment.vid and pfor=2)
					    where referanceid='".$_REQUEST['mid']."' and amount1>0";
					    $qr = $connection->createCommand($q)->queryAll();	
			        ?>
<tr>
    <td>
        <select onchange="" name="vvid<?php //echo $i;?>" id="vvid<?php //echo $i;?>">
            <option value="">Select Order</option>
            <?php 
                            foreach($qr as $row)
                            { 
                            ?>
            <option value="<?php echo $row['id'] ?>">
                <?php echo $row['COALESCE(trans.order_no,memberplot.plotno)'].'-'.$row['remarks'] ?></option>
            <?php 
                            } 
                            ?>
        </select>
    </td>
    <td>Advances</td>
    <td></td>
    <td>
        <input readonly="readonly" type="text" style="width:100px;height: 20px;" name="qamount" id="qamount"
            value="<?php echo $amr4['amount'] ?>" />
        <input type="hidden" name="advanceid" id="advanceid" value="<?php echo $amr4['id']; ?>">

        <input type="button" onClick="form_submit()" name="sub" id="sub" value="Adjust">

    </td>
</tr>
<?php
			    }
	    }
	
	public function actionSale_refund()
	{
	    $connection = Yii::$app->getDb();	
	    
        // $acc  = "SELECT *,memberplot.id as mid from memberplot
        // Left Join accounts ON (accounts.ref=memberplot.id AND accounts.type=1)
        // where accounts.id='".$_REQUEST['mid']."'";
        // $acc1 = $connection->createCommand($acc)->queryOne();  
        
        $i=1;
        $am  = "SELECT *,payment.id as pid from payment 
        left JOIN transaction ON (transaction.id = payment.vid) 
        where payment.referanceid='".$_REQUEST['mid']."' and payment.pfor=1  AND payment.remarks != 'Tax Paid'";
        $amr = $connection->createCommand($am)->queryAll(); 
        $s=0;
        foreach ($amr as $amr1)
        {
            $wherev='';
    		if(isset($_REQUEST['vid']) && $_REQUEST['vid']>0)
    		{$wherev=" AND vid!='".$_REQUEST['vid']."'";}
            
            $amm  = "SELECT sum(amount) as sm from payment where pfor=1 AND pro_id = '".$amr1['pid']."' $wherev";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            
            $r = $amr1['amount1'] - $amrm['sm']; 
			
			if($r!=0 && $s<20)
			{ 
			    $s=$s+1;
			    ?>
<tr>
    <td>
        <?php 
                        if($amr1['pfor'] == 1)
                        {
                            $sqlmm= "SELECT * FROM transaction where id='".$amr1['vid']."'";
                            $resultmm = $connection->createCommand($sqlmm)->queryOne();
                            echo $resultmm['vno'];
                        }
                        ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $amr1['pid']?>" />
    </td>
    <td><?php echo $amr1['remarks'];?></td>
    <td><?php echo  number_format($r,2) ;?>
        <input type="hidden" name="rowid<?php echo $i;?>" id="rowid<?php echo $i;?>" value="<?php echo $amr1['pid'];?>">
        <input type="hidden" name="oam<?php echo $i;?>" id="oam<?php echo $i;?>" value="<?php echo $r;?>">
        <input type="hidden" name="rem<?php echo $i;?>" id="rem<?php echo $i;?>" value="<?php echo $amr1['remarks'];?>">
    </td>
    <td>
        <input type="hidden" name="v<?php echo $i;?>" id="v<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $r?>" />
        <input type="text" style="width:100px;height: 20px;" onblur="tamount(<?php echo $i ?>)"
            name="qamount<?php echo $i;?>" id="qamount<?php echo $i;?>" value="0" />
    </td>
</tr>
<?php 
				$i=$i+1;
			} 	
		}
		?>
<tr style="display: none;">
    <td>
        <input type="text" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
        <input type="text" style="width:100px;height: 20px;" name="expid" id="expid" value="<?php echo $i;?>" />
    </td>
</tr>
<?php
	} 	
	public function actionDeductionfromca()
	{
	    $connection = Yii::$app->getDb();	
	    
        // $acc  = "SELECT *,memberplot.id as mid from memberplot
        // Left Join accounts ON (accounts.ref=memberplot.id AND accounts.type=1)
        // where accounts.id='".$_REQUEST['mid']."'";
        // $acc1 = $connection->createCommand($acc)->queryOne();  
        $ama  = "SELECT accounts.id as aid,memberplot.id as mpid,memberplot.plot_id as pid from accounts 
        left join memberplot on (accounts.ref=memberplot.id)
        where accounts.id='".$_REQUEST['mid']."'";
        $amra = $connection->createCommand($ama)->queryOne(); 
        $i=1;
        $am  = "SELECT * from cancellation 
        left join tax on (cancellation.deduction=tax.tax_id)
        where pid='".$amra['mpid']."'";
        $amr = $connection->createCommand($am)->queryOne(); 
        $s=0;
        if($amr['deduction_amount']>0)
        {
         echo '<tr><td><select onchange="gettax(1)" style="width:150px;border: 0px;border-bottom: 1px dotted;" name="acc11" id="acc11">
                   <option value="'.$amr['tax_id'].'">'.$amr['tax_title'].'</option>
         </select></td>
         <td><input type="text" id="narr1" value="Deducted Amount on Cancelation " name="narr1" style="border: 0px;border-bottom: 1px dotted;"></td>
         <td> <input style="border: 0px;border-bottom: 1px dotted;" placeholder="Tax" name="tax11" id="tax11" value="'.$amr['deduction_amount'].'" type="text"></td>
         </tr>';
        }   
		?>
<?php
	}    
	
	
	    
	public function actionPursp_res()
	{
	    $connection = Yii::$app->getDb();	
        $acc  = "SELECT * from plot_reserved_main where id='".$_REQUEST['mid']."'";
        $acc1 = $connection->createCommand($acc)->queryOne(); 
      //  print_r($acc1);exit;
        $i=1;
        $am  = "SELECT *,payment.id as pid from payment left JOIN transaction ON (transaction.id = payment.vid) where payment.vid='".$acc1['trans_id']."' and payment.pfor=1  AND payment.remarks != 'Tax Paid'";
        $amr = $connection->createCommand($am)->queryAll(); 
        $s=0;
        foreach ($amr as $amr1)
        {
            $wherev='';
		if(isset($_REQUEST['vid']) && $_REQUEST['vid']>0)
		{$wherev=" AND vid!='".$_REQUEST['vid']."'";}
            
            $amm  = "SELECT sum(amount) as sm from payment where pfor=1 AND pro_id = '".$amr1['pid']."' $wherev";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $amr1['amount1'] - $amrm['sm']; 
			
			if($r!=0 && $s<20)
			{ $s=$s+1;
			?>
<tr>
    <td>
        <?php 
			        if($amr1['pfor'] == 3)
			        {
                        echo $amr1['order_no'];
                    } 
                    if($amr1['pfor'] == 2)
                    {
                        $sqlmm= "SELECT * FROM memberplot where id='".$amr1['vid']."'";
                        $resultmm = $connection->createCommand($sqlmm)->queryOne();
                        echo $resultmm['plotno'];
                    };
                    if($amr1['pfor'] == 1)
                    {
                        $sqlmm= "SELECT * FROM transaction where id='".$amr1['vid']."'";
                        $resultmm = $connection->createCommand($sqlmm)->queryOne();
                        echo $resultmm['vno'];
                    };
                    ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $amr1['pid']?>" />
    </td>
    <td><?php echo $amr1['remarks'];?></td>
    <td><?php echo  number_format($r,2) ;?>
        <input type="hidden" name="rowid<?php echo $i;?>" id="rowid<?php echo $i;?>" value="<?php echo $amr1['pid'];?>">
        <input type="hidden" name="oam<?php echo $i;?>" id="oam<?php echo $i;?>" value="<?php echo $r;?>">
        <input type="hidden" name="rem<?php echo $i;?>" id="rem<?php echo $i;?>" value="<?php echo $amr1['remarks'];?>">
    </td>
    <td>
        <input type="hidden" name="v<?php echo $i;?>" id="v<?php echo $i;?>" style="width:100px;height: 20px;"
            readonly="readonly" value="<?php echo $r?>" />
        <input type="text" style="width:100px;height: 20px;" onblur="tamount(<?php echo $i ?>)"
            name="qamount<?php echo $i;?>" id="qamount<?php echo $i;?>" value="0" />
    </td>
</tr>
<?php 
				$i=$i+1;
			} 	
		}
		?>
<tr style="display: none;">
    <td>
        <input type="text" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
        <input type="text" style="width:100px;height: 20px;" name="expid" id="expid" value="<?php echo $i;?>" />
    </td>
</tr>

<?php
			    
	    }
	    
	    

	public function actionPursp1()
	{
		$connection = Yii::$app->getDb();	
     $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
    $acc1 = $connection->createCommand($acc)->queryOne(); 
    $i = 1;
       $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."'  AND remarks != 'Tax Paid' and pfor=1 ";
        $amr = $connection->createCommand($am)->queryAll(); 
        foreach ($amr as $amr1)
        {
         $amm  = "SELECT sum(amount) as sm from payment where pfor=1 AND pro_id = '".$amr1['id']."'";
            $amrm = $connection->createCommand($amm)->queryOne(); 
            $r = $amr1['amount1'] - $amrm['sm']; 
			
			if($r!=0){
        ?>

<tr>
    <td><?php if($amr1['order_no'] > 0){
                echo $amr1['order_no'];
              } else {
                 $sqlmm= "SELECT * FROM memberplot where id='".$amr1['vid']."'";
                 $resultmm = $connection->createCommand($sqlmm)->queryOne();
                 echo $resultmm['plotno'];
              }; ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $row['vid']?>" />
    </td>
    <td><?php echo $amr1['remarks'];?></td>
    <td><?php echo  number_format($amr1['amount1'],2) ;?>
        <input type="hidden" name="rowid<?php echo $i;?>" id="rowid<?php echo $i;?>" value="<?php echo $amr1['id'];?>">

        <input type="hidden" name="oam<?php echo $i;?>" id="oam<?php echo $i;?>" value="<?php echo $amr1['amount1'];?>">

        <input type="hidden" name="rem<?php echo $i;?>" id="rem<?php echo $i;?>" value="<?php echo $amr1['remarks'];?>">
    </td>
    <td>
        <input type="text" style="width:100px;height: 20px;" onblur="tamount(<?php echo $i ?>)"
            name="qamount<?php echo $i;?>" id="qamount<?php echo $i;?>" value="" />
    </td>
</tr>
<?php $i=$i+1; 
        echo '<input type="hidden" name="expid" id="expid" value="'.$i.'"/>';	
				}}

          ?>
<tr style="display: none;">
    <td>
        <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
    </td>
</tr>
<?php
                 exit;
			 		  $am4  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' AND pfor=1 AND (pro_id IS NULL OR pro_id = 0) AND remarks != 'Tax Paid'";
			 		$amr4= $connection->createCommand($am4)->queryOne();
			 		if($amr4['amount'] > 0)
			 		{
					$q  = "SELECT * from payment JOIN trans ON trans.trans_id = payment.vid where referanceid='".$_REQUEST['mid']."' AND (pfor=3)";
					$qr = $connection->createCommand($q)->queryAll();	
			// 		$j=$j+1;
			 		 ?>
<tr>
    <td>
        <select onchange="tamount1()" name="vvid<?php //echo $i;?>">
            <option value="">Select Order</option>
            <?php foreach($qr as $row){ ?>
            <option value="<?php echo $row['id'] ?>"><?php echo $row['order_no'] ?></option>
            <?php } ?>
        </select>
    </td>
    <td>Advances<?php //echo $qr['det'];?></td>
    <td><?php //echo  number_format($amr4['SUM(amount)'],2) ?></td>
    <td>
        <input readonly="readonly" type="text" style="width:100px;height: 20px;" name="qamount<?php //echo $i;?>"
            id="qamount<?php //echo $i;?>" value="<?php echo $amr4['amount'] ?>" />
    </td>
    <input type="hidden" name="advanceid" id="advanceid" value="<?php echo $amr4['id']; ?>">
</tr>
<?php  }
				
			
			
	}
	public function actionPurspsa()
	{
		$connection = Yii::$app->getDb();			
			
				$am  = "SELECT id from payment where referanceid='".$_REQUEST['mid']."' AND pfor=7";
				$amr = $connection->createCommand($am)->queryAll();	
					
				$i=0;$j=0;
				foreach ($amr as $row)
				{
					
					
					$am1  = "SELECT * from payment where id='".$row['id']."'";
					$amr1 = $connection->createCommand($am1)->queryOne();	
					
					
					
					$am3  = "SELECT SUM(amount) from payment where referanceid='".$_REQUEST['mid']."' AND sid='".$amr1['id']."' AND pfor=1";
					$amr3= $connection->createCommand($am3)->queryOne();
					
					$r=$amr1['amount1']-$amr3['SUM(amount)'];
					if($r!==0)
					{
					    $i=$i+1;
						?>
<tr>
    <td><?php echo $amr1['remarks']; ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $row['id']?>" />
    </td>
    <td><?php echo  number_format($r,2) ?></td>
    <td>
        <input type="text" style="width:100px;height: 20px;" onblur="tamount()" name="qamount<?php echo $i;?>"
            id="qamount<?php echo $i;?>" value="0" />
    </td>
</tr>
<?php $r=0;} 	
				}
					 $am4  = "SELECT SUM(amount) from payment where referanceid='".$_REQUEST['mid']."' AND pfor=1";
					$amr4= $connection->createCommand($am4)->queryOne();
					if($amr4['SUM(amount)']>0)
					{
					$q  = "SELECT Distinct(vid),detail as det,trans.trans_id as qid,orderno from payment
					Left Join trans on (payment.vid=trans.trans_id) 
					where pfor=3";
					$qr = $connection->createCommand($q)->queryAll();	
					$j=$j+1;
					 ?>
<tr>
    <td>Advances<?php //echo $qr['det'];?></td>
    <td><?php //echo  number_format($amr4['SUM(amount1)'],2) ?></td>
    <td>
        <input readonly="readonly" type="text" style="width:100px;height: 20px;" name="qamount<?php //echo $i;?>"
            id="qamount<?php //echo $i;?>" value="<?php echo $amr4['SUM(amount)'] ?>" />
    </td>
</tr>
<?php  }
				
				?>
<tr style="display: none;">
    <td>
        <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
    </td>
</tr>
<?php
			
	}
	public function actionPursp111()
	{
		$connection = Yii::$app->getDb();	
     $acc  = "SELECT * from accounts where id='".$_REQUEST['mid']."'";
    $acc1 = $connection->createCommand($acc)->queryOne(); 
    $i = 1;
       $am  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."'  AND remarks != 'Tax Paid'";
        $amr = $connection->createCommand($am)->queryAll(); 
        foreach ($amr as $amr1)
        {	?>

<tr>
    <td><?php if($amr1['order_no'] > 0){
                echo $amr1['order_no'];
              } else {
                 $sqlmm= "SELECT * FROM memberplot where id='".$amr1['vid']."'";
                 $resultmm = $connection->createCommand($sqlmm)->queryOne();
                 echo $resultmm['plotno'];
              }; ?>
        <input type="hidden" name="vid<?php echo $i;?>" style="width:100px;height: 20px;" readonly="readonly"
            value="<?php echo $row['vid']?>" />
    </td>
    <td><?php echo $amr1['remarks'];?></td>
    <td><?php echo  number_format($r,2) ;?>
        <input type="hidden" name="rowid<?php echo $i;?>" id="rowid<?php echo $i;?>" value="<?php echo $amr1['id'];?>">

        <input type="hidden" name="oam<?php echo $i;?>" id="oam<?php echo $i;?>" value="<?php echo $amr1['amount1'];?>">

        <input type="hidden" name="rem<?php echo $i;?>" id="rem<?php echo $i;?>" value="<?php echo $amr1['remarks'];?>">
    </td>
    <td>
        <input type="text" style="width:100px;height: 20px;" onblur="tamount(<?php echo $i ?>)"
            name="qamount<?php echo $i;?>" id="qamount<?php echo $i;?>" value="<?php echo $amr1['amount1'] ;?>" />
    </td>
</tr>
<?php $i=$i+1; 
        echo '<input type="hidden" name="expid" id="expid" value="'.$i.'"/>';	
				}

          ?>
<tr style="display: none;">
    <td>
        <input type="hidden" style="width:100px;height: 20px;" name="tq" id="tq" value="<?php echo $i;?>" />
    </td>
</tr>
<?php
                 exit;
			 		  $am4  = "SELECT * from payment where referanceid='".$_REQUEST['mid']."' AND pfor=1 AND (pro_id IS NULL OR pro_id = 0) AND remarks != 'Tax Paid'";
			 		$amr4= $connection->createCommand($am4)->queryOne();
			 		if($amr4['amount'] > 0)
			 		{
					$q  = "SELECT * from payment JOIN trans ON trans.trans_id = payment.vid where referanceid='".$_REQUEST['mid']."' AND (pfor=3)";
					$qr = $connection->createCommand($q)->queryAll();	
			// 		$j=$j+1;
			 		 ?>
<tr>
    <td>
        <select onchange="tamount1()" name="vvid<?php //echo $i;?>">
            <option value="">Select Order</option>
            <?php foreach($qr as $row){ ?>
            <option value="<?php echo $row['id'] ?>"><?php echo $row['order_no'] ?></option>
            <?php } ?>
        </select>
    </td>
    <td>Advances<?php //echo $qr['det'];?></td>
    <td><?php //echo  number_format($amr4['SUM(amount)'],2) ?></td>
    <td>
        <input readonly="readonly" type="text" style="width:100px;height: 20px;" name="qamount<?php //echo $i;?>"
            id="qamount<?php //echo $i;?>" value="<?php echo $amr4['amount'] ?>" />
    </td>
    <input type="hidden" name="advanceid" id="advanceid" value="<?php echo $amr4['id']; ?>">
</tr>
<?php  }
				
			
			
	}
	
	 public function actionManage()
    {

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->orderBy(['id' => SORT_DESC,]);
        if($_SESSION["user_array"]["usertype"]==2)
		{
		$dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
		}
        $dataProvider->pagination->pageSize=$request->get("pagesize",20);
         if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
		$dataProvider->pagination->page=($_REQUEST['page']-1);
            return $this->render('manage', [
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
    }
    
    public function actionRejected_payments()
    {
        return $this->render('rejected_payments');    
    }

   

// 	public function actionIns_recieved()
//     {

//         $request = Yii::$app->request;
        
//         $searchModel = new TransactionSearch();

//         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
// 		$dataProvider->query->orderBy(['id' => SORT_DESC,]);
        
//         if($_SESSION["user_array"]["usertype"]==2)
// 		{
// 		    $dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
// 		}
		
// 		$dataProvider->query->andFilterWhere(['transaction.status_type'=>0]);
// 		$dataProvider->query->andFilterWhere(['IN','transaction.vtype',[4,3]]);
		
// 		$dataProvider->pagination->pageSize=$request->get("pagesize",20);
//         if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
// 		$dataProvider->pagination->page=($_REQUEST['page']-1);
		

//         if($request->isAjax)
//         {
//             return $this->renderPartial('ins_recieved', [
//                 'searchModel' => $searchModel,
//                 'dataProvider' => $dataProvider,
//             ]);
//         }
//         else
//         {
// 			$searchModel1='';
// 			$dataProvider1='';
//             return $this->render('ins_recieved', [
//                 'searchModel1' => $searchModel1,
//                 'dataProvider1' => $dataProvider1,
// 				'searchModel' => $searchModel,
//                 'dataProvider' => $dataProvider,
//             ]);
//         }
//     }

    public function actionMultireceipt()
    {
      
    $insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
            //$model->cheque_date = 
        }
        if ($model->load(Yii::$app->request->post()) ) 
        {
          $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
        
        //  exit();
          if($model->save()){
       //     echo "string";
      $insert_id = $model->id;
      $conn=Yii::$app->getDb(); 
    //  $sqlt2 = "SELECT * from cconfig where type=15";
      //$resultt2 = $conn->createCommand($sqlt2)->queryOne();
      if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
      if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
        $ta=0; 
        
        // if($model->vtype==3 || $model->vtype==4)
        // {
//echo $model->vtype;
            if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0)
            {
                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',
                referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount)."',
                date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',
                type='0',jvid='0',gl=0";
                \Yii::$app->db->createCommand($sql)->execute(); 
                $q=1;
                do
                {
                    if(isset($_POST['v'.$q]) && $_POST['v'.$q]>0)
                    {
                        $sqlrec = "SELECT * from payment where id='".$_POST['ins'.$q]."'";
                        $resultrec = $conn->createCommand($sqlrec)->queryOne();
                        $status=0;
                        if($resultrec['remarks']=='Receivable Amount'){$status=1;}

                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',
                        branch_id='".$model->branch_id."',referanceid='".$_POST['acc'.$q]."',vid='".$insert_id."',
                        sid=".$_POST['ins'.$q].", pfor='1',amount1='".$_POST['v'.$q]."',
                        date='".$model->create_date."',status='1',remarks='".$_POST['rem'.$q]."',
                        createdate='".date('Y-m-d')."',type='0',jvid='".$status."',gl=0";
                        \Yii::$app->db->createCommand($sql)->execute();
                    }             
                    $q=$q+1;
                }while($q <= ($_POST['totalexp']));           
            
          }
        //    else
        //   {
        //   //echo 'here';      
        //     if(isset($_POST['states-select'])){
        //   $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount1='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
        //        \Yii::$app->db->createCommand($sql)->execute();
              
        //       $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
        //       \Yii::$app->db->createCommand($sql)->execute();
        //     }}
        // }
        
        $a='Multi Receive Payment';
        $at='Create';
        $u=$insert_id;
        $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);

    return 1;
        }
        else
        {
            
    return 0;
        }
      
      return $this->redirect(['index']);
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('multireceipt', [
                    'model' => $model,
                ]);
        









            }
            else
            {
        
                return $this->render('multireceipt', [
                    'model' => $model,
                ]);
            }
        }
    
    }
    public function actionDelete_salary($id)
    {
        $connection = Yii::$app->getDb();		
			$connection ->createCommand()->delete('payment', 'vid='.$id.'')->execute();
        
        $this->findModel($id)->delete();

        return $this->redirect(['index1_salary']);
    }
    
     public function actionTransfer_payment()
    {
        return $this->render('transfer_payment');    
    }
     public function actionCreate1_salary()
    {
		$insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
           
        }
        $connection = Yii::$app->getDb();
        //echo $model->create_date;exit;
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          
        if ($model->load(Yii::$app->request->post())) 
        {     
            $model->cheque_date=date("Y-m-d", strtotime( $model->cheque_date));
        
            $model->vno=($result_max['MAX(vno)']+1);
        
            if($model->save()){
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			$sqlt2 = "SELECT * from cconfig where type=15";
			$resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$model->cash_id;}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM taxdefination where id='".$_POST['acc1'.$j]."'";
	                $result = $conn->createCommand($sql)->queryOne();
					$tac=0;$tad=0;
					if($model->vtype==1 or $model->vtype==2){$tac=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					if($model->vtype==3 or $model->vtype==4){$tad=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					
					$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount='".$tad."',amount1='".$tac."',date='".date('Y-m-d')."',status='1',type='1',remarks='OK',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
						}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
				if($model->vtype==1 || $model->vtype==2)
				{
					if(isset($_POST['vvid']) && !empty($_POST['vvid']))
					{
					    $sql = "Update payment SET salecenter='".$_SESSION['user_array']['salecenter']."',sid=".$_POST['vvid']." where referanceid='".$_POST['states-select']."' AND pfor='1' AND sid IS NULL";
			        	\Yii::$app->db->createCommand($sql)->execute();
					}
					
					
					if(isset($_POST['tq']) && $_POST['tq']>0)
					{
						$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			            \Yii::$app->db->createCommand($sql)->execute();
						$q=1;
						do
						{
						    if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
						    {
							$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',sid=".$_POST['vid'.$q].", pfor='1',amount='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        		\Yii::$app->db->createCommand($sql)->execute();
							}							
							$q=$q+1;
						}while($q < ($_POST['tq']+1));
						
					}
					else
					{			
    					$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
    			        \Yii::$app->db->createCommand($sql)->execute();
    			        
    			        $sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
    			        \Yii::$app->db->createCommand($sql)->execute();
					}
				}
				
				if($model->vtype==3 || $model->vtype==4)
				{
					if(isset($_POST['vvid']) && !empty($_POST['vvid']))
						{
							$sql = "Update payment SET salecenter='".$_SESSION['user_array']['salecenter']."',sid=".$_POST['vvid']." where referanceid='".$_POST['states-select']."' AND pfor='1' AND sid IS NULL";
			        		\Yii::$app->db->createCommand($sql)->execute();
						}			
						
							
					if(isset($_POST['tq']) && $_POST['tq']>0)
					{
					$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
					
						$q=1;
						do
						{
						if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
						{
							$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',sid=".$_POST['vid'.$q].", pfor='1',amount1='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
							\Yii::$app->db->createCommand($sql)->execute();
					}
							$q=$q+1;
						}while($q < ($_POST['tq']+1));					
						
					}
					else{	
					$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount1='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
			        $sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			            \Yii::$app->db->createCommand($sql)->execute();
					}
					
				}
				
					
				if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0){
				$i=1;
				do{
				 if(isset($_POST['acc'.$i]) and isset($_POST['acc'.$i])=='on')
				 {
					$connection = Yii::$app->getDb();
					$status=1;
					
					$sql = "INSERT into payment SET salecenter='".$_SESSION['user_array']['salecenter']."',referanceid='".$_POST['acc'.$i]."',vid=".$insert_id.", pfor='1',amount='".($_POST['dam'.$i])."',date='".date('Y-m-d')."',status='1',remarks='".$_POST['details'.$i]."',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 }
				$i=$i+1;
				}while($i < ($_POST['totalexp']+1));
				}
				
			        $a='Pay Bill';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1_salary']);}
			else
            {
				// print_r($model->errors);exit;
                return $this->render('create1_salary', [
                    'model' => $model,
                ]);
            }
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create1_salary', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionCreate1_salarym()
    {
		$insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          $model->vno=($result_max['MAX(vno)']+1);
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
        {
			if($model->vtype==1){$model->bank_id=$model->cash_id;}
			$model = $this->findModel($model->id);
		    $model->vno=($result_max['MAX(vno)']+1);
		    
			$model->save();
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			$sqlt2 = "SELECT * from cconfig where type=15";
			$resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$model->cash_id;}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM taxdefination where id='".$_POST['acc1'.$j]."'";
	                $result = $conn->createCommand($sql)->queryOne();
					$tac=0;$tad=0;
					if($model->vtype==1 or $model->vtype==2){$tac=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					if($model->vtype==3 or $model->vtype==4){$tad=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount='".$tad."',amount1='".$tac."',date='".date('Y-m-d')."',status='1',type='1',remarks='OK',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
						}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
				if($model->vtype==1 || $model->vtype==2)
				{
					
					
					
					if(isset($_POST['tq']) && $_POST['tq']>0)
					{
						$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			            \Yii::$app->db->createCommand($sql)->execute();
						$q=1;
						do
						{
						    if(isset($_POST['qamount'.$q]) && $_POST['qamount'.$q]>0)
						    {
							$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['eid'.$q]."',vid='".$insert_id."',sid=0, pfor='1',amount='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        		\Yii::$app->db->createCommand($sql)->execute();
							}							
							$q=$q+1;
						}while($q < ($_POST['tq']+1));
						
					}
					else
					{			
    					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
    			        \Yii::$app->db->createCommand($sql)->execute();
    			        
    			        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
    			        \Yii::$app->db->createCommand($sql)->execute();
					}
				}
				
			
				
					
				if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0){
				$i=1;
				do{
				 if(isset($_POST['acc'.$i]) and isset($_POST['acc'.$i])=='on')
				 {
					$connection = Yii::$app->getDb();
					$status=1;
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['acc'.$i]."',vid=".$insert_id.", pfor='1',amount='".($_POST['dam'.$i])."',date='".date('Y-m-d')."',status='1',remarks='".$_POST['details'.$i]."',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 }
				$i=$i+1;
				}while($i < ($_POST['totalexp']+1));
				}
				
			        $a='Pay Bill';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1_salary']);
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create1_salarym', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionPay_loan()
    {
		$insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          $model->vno=($result_max['MAX(vno)']+1);
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
        {if($model->vtype==1){$model->bank_id=$_POST['ref'];}
        $model = $this->findModel($model->id);
		    $model->vno=($result_max['MAX(vno)']+1);
		    
		    
			$model->save();
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
				  
				  if(empty($_POST['narr'.$j])){$_POST['narr'.$j]='Expense';}
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['acc1'.$j]."',vid=".$insert_id.", pfor='1',amount='".$_POST['tax1'.$j]."',date='".date('Y-m-d')."',status='1',type='1',
					remarks='".$_POST['narr'.$j]."',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
						}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount1='".$ta."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
				
				if($model->vtype==3 || $model->vtype==4 || $model->vtype==1 || $model->vtype==2)
				{
	
            $amt = ($model->amount)+$ta;
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();

			        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			            \Yii::$app->db->createCommand($sql)->execute();
					
				}
				
					
				
			        $a='Pay Expense';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1_salary']);
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('pay_loan', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionUpdate_salary($id)
    {
        $model = $this->findModel($id);
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        if ($model->load(Yii::$app->request->post()))
        {
            $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
			//exit;
			if($model->vtype==1){$model->bank_id=$_POST['ref'];}
        if($model->save()) {
        	$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			$sqlt2 = "SELECT * from cconfig where type=15";
			$resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM taxdefination where id='".$_POST['acc1'.$j]."'";
	                $result = $conn->createCommand($sql)->queryOne();
					$tac=0;$tad=0;
					if($model->vtype==1 or $model->vtype==2){$tac=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					if($model->vtype==3 or $model->vtype==4){$tad=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount='".$tad."',amount1='".$tac."',date='".date('Y-m-d')."',status='1',type='1',remarks='OK',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
						}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}	
				
				if($model->vtype==1 || $model->vtype==2)
				{
					
	                
					
					if(isset($_POST['tq']) && $_POST['tq']>0)
					{
						
						$q=1;
						do
						{
							
						if($_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
						{
							$sql = "Update payment SET salecenter='".$model->salecenter."',amount='".$_POST['qamount'.$q]."' where referanceid='".$model->pt_id."' AND vid=".$insert_id." AND pfor='1' AND  sid='".$_POST['vid'.$q]."'";
			        		\Yii::$app->db->createCommand($sql)->execute();
							
						}
														
							$q=$q+1;
						}while($q < ($_POST['tq']+1));
					}		
					$sql1 = "Update payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0' where referanceid='".$ref."' AND vid=".$insert_id." AND pfor='1'";
	                \Yii::$app->db->createCommand($sql1)->execute();
	                
	                $sql1 = "Update payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0' where referanceid='".$model->pt_id."' AND vid=".$insert_id." AND pfor='1'";
	                \Yii::$app->db->createCommand($sql1)->execute();
					
	                
				}
				
				if($model->vtype==3 || $model->vtype==4)
				{ 
					if(isset($_POST['tq']) && $_POST['tq']>0)
					{
						$q=1;
						do
						{
						if($_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
						{
							$sql = "Update payment SET salecenter='".$model->salecenter."',amount1='".$_POST['qamount'.$q]."' where referanceid='".$model->pt_id."' AND vid=".$insert_id." AND pfor='1' AND  sid='".$_POST['vid'.$q]."'";
			        		\Yii::$app->db->createCommand($sql)->execute();
						}
														
							$q=$q+1;
						}while($q < ($_POST['tq']+1));
						
					}			
							
					$sql1 = "Update payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0' where referanceid='".$ref."' AND vid=".$insert_id." AND pfor='1'";
	                \Yii::$app->db->createCommand($sql1)->execute();
	                
	                $sql12 = "Update payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0' where referanceid='".$model->pt_id."' AND vid=".$insert_id." AND pfor='1'";
	                \Yii::$app->db->createCommand($sql12)->execute();
	                
				}
				
				if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0){
				$i=1;
				do{
				 if(isset($_POST['acc'.$i]) and isset($_POST['acc'.$i])=='on')
				 {
					$connection = Yii::$app->getDb();
					$status=1;
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['acc'.$i]."',vid=".$insert_id.", pfor='1',amount='".($_POST['dam'.$i])."',date='".date('Y-m-d')."',status='1',remarks='".$_POST['details'.$i]."',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 }
				$i=$i+1;
				}while($i < ($_POST['totalexp']+1));
				}
                    $a='Pay Bill';
                    $at='Update';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
			return $this->redirect(['index1_salary']);
        }
        else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update_salary', [
                    'model' => $model,
                ]);
            }
        }
        }
        else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update_salary', [
                    'model' => $model,
                ]);
            }
        }
    }
   
    
    
    

    
    public function actionCreate12()
    {
		$insert_id=0;
        
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
            //$model->cheque_date = 
        }
        if ($model->load(Yii::$app->request->post()) ) 
        {

          $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
          $model->fc=0;
          $model->rate=0;
          $tt=0;
          $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          if($model->vtype==3){$model->bank_id=$_POST['ref'];}
          $model->vno=($result_max['MAX(vno)']+1);
          if($model->save()){
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
		//	$sqlt2 = "SELECT * from cconfig where type=15";
			//$resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				if($model->salecenter==''){$model->salecenter=0;}
				
				
				if($model->vtype==3 || $model->vtype==4)
				{
//echo $model->vtype;

					
					
					if(isset($_POST['tq']) && $_POST['tq'] > 0)
 					{

            if(isset($_POST['tqval']) && $_POST['tqval'] > 0){

            //echo "insert ".$_POST['tqval'];

     


             $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.",sid=0, pfor='1',amount='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
          
              \Yii::$app->db->createCommand($sql)->execute();
// /// echo
$q=1;
		do  
           {
           if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
						{
              // echo "string3"; 
         
            $comm1 = Commissionsub::find()->where(['pid' => $_POST['vid'.$q], 'status' => NULL])->all();
           
            foreach ($comm1 as $comm) { 
            if($comm->amount != '' || $comm->amount != 0){
// echo "string";
              $com = Commission::find()->where(['id' => $comm->comid])->one();
    $connection = Yii::$app->db;  

              $sqltsa2 = "SELECT * from cconfig where type=17";
                        $config = $connection->createCommand($sqltsa2)->queryOne();
             

              $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vid'.$q].", pfor='10',amount1='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0'";


                  \Yii::$app->db->createCommand($sql)->execute();

              $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vid'.$q].", pfor='10',amount='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0'";


                  \Yii::$app->db->createCommand($sql)->execute();

                  $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";


                  \Yii::$app->db->createCommand($sql)->execute();

            }
          }

							$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',sid=".$_POST['vid'.$q].", pfor='1',amount1='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',remarks='".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        		\Yii::$app->db->createCommand($sql)->execute();
							}							
							$q=$q+1;
						}while($q < ($_POST['tq']+1));
						
						
					} }
					else
				 	{
				 	    if($model->amount!==0){
				 	    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount1='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			            \Yii::$app->db->createCommand($sql)->execute();
			        
			           $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount-$tt)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			           \Yii::$app->db->createCommand($sql)->execute();
				 	    }
				 	}
					
				}
				    $mobile=0;
                $ref='';$chk='';
                if($model->vtype==2 || $model->vtype==4)
                {
                    $ref='Ref # '.$model['vno'];
                    $chk='Cheque # '.$model['cheque_no'];
                }
                
                $sql_phone = "SELECT *  from accounts
                Left Join memberplot ON (memberplot.id=accounts.ref AND accounts.type=1)
                Left Join members ON (members.id=memberplot.member_id)
                where accounts.id='".$model->pt_id."'";
                $result_phone = $connection->createCommand($sql_phone)->queryOne();
                
                if(!empty($result_phone['phone']))
                {
                    $mb_array=array();
                    $mb_array = explode(",", $result_phone['phone']);
                            
                    for($i=0;$i<count($mb_array);$i++)
                    {
                        $mb_array_i=0;
                        if(strlen($mb_array[$i])>9 && strlen($mb_array[$i])<=15)
                        {
                            $mb_array_i=str_replace(["-", "–"], '', $mb_array[$i]);   
                            $mobile='+92'.(substr($mb_array_i, -10));
                            $message='Dear Member, 
Thank you for your payment of PKR '.number_format($model->amount,0).'/-   for Your MS # '.$result_phone['plotno'].' '.$ref.' '.$chk.'
Your ledger has been updated and can be verified online.
Contact Us at : 03041118888';
                            if($model->create_date==date('Y-m-d'))
                            {
                                // Yii::$app->mycomponent->Send_text_msg($mobile,$message);
                            }
                        }
                    }
                } 
				
				echo "Successful Submission";
			
				    $a='Receive Installments';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);

                  //  $sql= "SELECT *, p.id as pid FROM memberplot p JOIN accounts ON accounts.ref = p.id where accounts.id='".$_POST['states-select']."' AND accounts.type = 1";
                //  $result = $conn->createCommand($sql)->queryOne();
                   echo '<br><a href="'.Yii::$app->urlManager->baseUrl.'/Print/transaction.php?id='.$insert_id.'" class="btn btn-primary" target="_blank">Print Details (BG)</a>';
                   echo '<a href="'.Yii::$app->urlManager->baseUrl.'/tcpdf/print/reciept.php?id='.$insert_id.'&mem='.$_SESSION['user_array']['id'].'" style="margin-left: 2%;" class="btn btn-danger" target="_blank">Print Details</a>';
			//return $this->redirect('tcpdf/print/paymentdetails.php?id='.$result['pid']);
       } } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
        









            }
            else
            {
				
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
    }	
	 
	 
	public function actionCreate_rec1()
    {
        
		$insert_id=0;
        
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
            //$model->cheque_date = 
        }
        if ($model->load(Yii::$app->request->post()) ) 
        {

          $model->cheque_date = date("Y-m-d", strtotime($model->cheque_date));
          $model->fc=0;
          $model->rate=0;
          $model->status_type=5;
          $tt=0;
          $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          if($model->vtype==3){$model->bank_id=$_POST['ref'];}
          $model->vno=($result_max['MAX(vno)']+1);
          if($model->save()){
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
		//	$sqlt2 = "SELECT * from cconfig where type=15";
			//$resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				
				$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',sid=0,vid=".$insert_id.", pfor='1',amount1='".$model->amount."',amount='0',date='".$model->create_date."',status='1',type='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."'";
				\Yii::$app->db->createCommand($sql)->execute();
				
				$sql1 = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',sid=0,vid=".$insert_id.", pfor='1',amount1='0',amount='".$model->amount."',date='".$model->create_date."',status='1',type='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."'";
				\Yii::$app->db->createCommand($sql1)->execute();
				
				echo "Successful Submission";
			
				$a='Receive Payment';
                $at='Create';
                $u=$insert_id;
                $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
                
                // $mobile='+923410050430';
                // $message='Your instalment has been received. Thank you.';
                // Yii::$app->mycomponent->Send_text_msg($mobile,$message);
                
                
                
                
                  //  $sql= "SELECT *, p.id as pid FROM memberplot p JOIN accounts ON accounts.ref = p.id where accounts.id='".$_POST['states-select']."' AND accounts.type = 1";
                //  $result = $conn->createCommand($sql)->queryOne();
                   echo '<br><a href="'.Yii::$app->urlManager->baseUrl.'/Print/transaction.php?id='.$insert_id.'" class="btn btn-primary" target="_blank">Print Details (BG)</a>';
                   echo '<a href="'.Yii::$app->urlManager->baseUrl.'/Print/transaction_rec.php?id='.$insert_id.'&mem='.$_SESSION['user_array']['id'].'" style="margin-left: 2%;" class="btn btn-danger" target="_blank">Print Details</a>';
			//return $this->redirect('tcpdf/print/paymentdetails.php?id='.$result['pid']);
       } } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
        









            }
            else
            {
				
                return $this->render('create_rec', [
                    'model' => $model,
                ]);
            }
        }
    }	
	 
	 
	 public function actionCreate1copy()
    {
		$insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }

        if($model->load(Yii::$app->request->post())){
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
        $connection = Yii::$app->getDb();
        
        if(!empty($model->remarks)){$model->remarks=str_replace("'","",$model->remarks);}
        //echo $model->remarks;exit;
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          if($model->vtype==1){$model->bank_id=$_POST['ref'];}
          $model->vno=($result_max['MAX(vno)']+1);
        if ($model->save()) 
        {
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				$tt=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount1='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',remarks='Tax Paid',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
					$tt=$tt+$_POST['tax1'.$j];	}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
				
				$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount1='0',amount='".$model->amount."',date='".$model->create_date."',status='1',type='0',remarks='".$model->remarks."',createdate='".date('Y-m-d')."'";
			    \Yii::$app->db->createCommand($sql)->execute();
				
				$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='0',amount1='".($model->amount-$tt)."',date='".$model->create_date."',status='1',type='0',remarks='".$model->remarks."',createdate='".date('Y-m-d')."'";
			    \Yii::$app->db->createCommand($sql)->execute();
				
			        $a='Pay Bill';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1']);
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create1', [
                    'model' => $model,
                ]);
            }
        }}else{

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create1', [
                    'model' => $model,
                ]);
            }
        }
    }
    
    public function actionUpdate_refundcopy($id)
    {
		$insert_id=0;
        $model = $this->findModel($id);
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }

        if($model->load(Yii::$app->request->post()))
        {
            $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
            $connection = Yii::$app->getDb();
		    if($model->vtype==3){$model->bank_id=$_POST['ref'];}
		    if($model->vtype==1){$model->bank_id=$_POST['ref'];}
         
         
            if ($model->save()) 
            { 
                $sql3d = "Delete from payment Where pfor=1 AND vid='".$model->id."'";
                \Yii::$app->db->createCommand($sql3d)->execute();
                
                $tt=0;
			    $insert_id = $model->id;
			    $conn=Yii::$app->getDb();  
			    if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			    if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				
				$tt=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0)
				{
				    $j=1;
				    do
				    {
				        if(isset($_POST['acc1'.$j]))
				        {
					        $connection = Yii::$app->getDb();
					        $status=1;
					        $sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	                        $result = $conn->createCommand($sql)->queryOne();
				            $ta = $ta+$_POST['tax1'.$j];
					
					        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", 
					        pfor='1',amount1='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',remarks='Refund Tax',createdate='".date('Y-m-d')."'";
			                \Yii::$app->db->createCommand($sql)->execute();
					        $tt=$tt+$_POST['tax1'.$j];	
				        
				        }
				        $j=$j+1;
				    }while($j < ($_POST['totalexp1']+1));
				}
				//$sqltsa2 = "SELECT * from cconfig where type=19";
                //$config = $conn->createCommand($sqltsa2)->queryOne();
                //$_POST['states-select']=$config['aid'];
                $model = $this->findModel($insert_id);
                $model->amount=($model->amount-($tt));
                $model->save();
                
                $toinv=0;
				if(isset($_POST['tq']) && $_POST['tq']>0)
				{
				    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',
				    amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',
				    type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 	
				 	$q=1;
				 	do
				 	{
				 	    if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
				 		{
				 		    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',
				 		    pro_id='".$_POST['vid'.$q]."', pfor='1',amount='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',
				 		    remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         		\Yii::$app->db->createCommand($sql)->execute();
				 			$toinv=$toinv+$_POST['qamount'.$q];
				 		    
				 		}	
				 		$q=$q+1;
				 	}while($q < ($_POST['tq']+1));
				 	
				}
				
				$a='Refund';
                $at='Update';
                $u=$insert_id;
               $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
			    return $this->redirect(['index1']);
            } 
            else 
            {
                return $this->render('update_refund', ['model' => $model]);
            }
            
        }
        else
        {
            return $this->render('update_refund', ['model' => $model]);
        }
    }
    
    
    public function actionRefundcopy
    ()
    {
		$insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }

        if($model->load(Yii::$app->request->post()))
        {
            $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
            $connection = Yii::$app->getDb();
		    if($model->vtype==3){$model->bank_id=$_POST['ref'];}
		    if($model->vtype==1){$model->bank_id=$_POST['ref'];}
         
            $sql_max = "SELECT MAX(vno) from transaction";
            $result_max = $connection->createCommand($sql_max)->queryOne();
            $model->vno=($result_max['MAX(vno)']+1);
            if($model->save()) 
            { 
                $tt=0;
			    $insert_id = $model->id;
			    $conn=Yii::$app->getDb();  
			    if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			    if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				//$sqltsa2 = "SELECT * from cconfig where type=19";
                //$config = $conn->createCommand($sqltsa2)->queryOne();
                //$_POST['states-select']=$config['aid'];
                $model->salecenter=1;
                
                $tt=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0)
				{
				    $j=1;
				    do
				    {
				        if(isset($_POST['acc1'.$j]))
				        {
					        $connection = Yii::$app->getDb();
					        $status=1;
					        $sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	                        $result = $conn->createCommand($sql)->queryOne();
				            $ta = $ta+$_POST['tax1'.$j];
					
					        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", 
					        pfor='1',amount1='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',remarks='Refund Tax',createdate='".date('Y-m-d')."'";
			                \Yii::$app->db->createCommand($sql)->execute();
					        $tt=$tt+$_POST['tax1'.$j];	
				        
				        }
				        $j=$j+1;
				    }while($j < ($_POST['totalexp1']+1));
				}
                
                $model = $this->findModel($model->id);
                $model->amount=($model->amount-($tt));
                $model->save();
                
                $toinv=0;
				if(isset($_POST['tq']) && $_POST['tq']>0)
				{
				    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',
				    amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',
				    type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 	
				 	$q=1;
				 	do
				 	{
				 	    if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
				 		{
				 		    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',
				 		    pro_id='".$_POST['vid'.$q]."', pfor='1',amount='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',
				 		    remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         		\Yii::$app->db->createCommand($sql)->execute();
				 			$toinv=$toinv+$_POST['qamount'.$q];
				 		    
				 		}	
				 		$q=$q+1;
				 	}while($q < ($_POST['tq']+1));
				 	
				}
				
				$a='Refund';
                $at='Create';
                $u=$insert_id;
                $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
			    return $this->redirect(['index1']);
            } 
            else 
            {
                return $this->render('refund', ['model' => $model]);
            }
            
        }
        else
        {
            return $this->render('refund', ['model' => $model]);
        }
    }
    
    
    public function actionCreate1_ta()
    {
		$insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }

        if($model->load(Yii::$app->request->post())){
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
        $connection = Yii::$app->getDb();
		if($model->vtype==3){$model->bank_id=$_POST['ref'];}
		if($model->vtype==1){$model->bank_id=$_POST['ref'];}
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          $model->vno=($result_max['MAX(vno)']+1);
        if ($model->save()) 
        { 
			$insert_id = $model->id;
			$conn=Yii::$app->getDb();  
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				$sqltsa2 = "SELECT * from cconfig where type=19";
                        $config = $conn->createCommand($sqltsa2)->queryOne();
                        $_POST['states-select']=$config['aid'];
				
				
				$tt=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount1='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',remarks='".$_POST['narr'.$j]."',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
					$tt=$tt+$_POST['tax1'.$j];	}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
			
					$toinv=0;
					if(isset($_POST['tq']) && $_POST['tq']>0 && isset($_POST['qamount1']) && $_POST['qamount1']>0)
				 	{
				 		$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-($tt))."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			            \Yii::$app->db->createCommand($sql)->execute();
				 		$q=1;
				 		do
				 		{
				 		if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
				 		{
				 			
				 			
				 			$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',pro_id=".$_POST['vid'.$q].", pfor='1',amount='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         		\Yii::$app->db->createCommand($sql)->execute();
				 			$toinv=$toinv+$_POST['qamount'.$q];
				 		    
				 		}							
				 			$q=$q+1;
				 		}while($q < ($_POST['tq']+1));
						
						if($_POST['qamount1']>0 && $toinv!=$model->amount ){
						    
						     $sql1 = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',pro_id='0', pfor='1',amount='".($model->amount-$toinv)."',date='".$model->create_date."',status='1',remarks='Advance Payment',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         		\Yii::$app->db->createCommand($sql1)->execute();
				 			
						    
						}
						
				 	}
				 	else
				 	{
				 	    if($model->amount !==0){
				 	    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         \Yii::$app->db->createCommand($sql)->execute();
			        
			         $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-$tt)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         \Yii::$app->db->createCommand($sql)->execute();
				 	    }
				 	        
				 	    }
					
				
			
				
			        $a='Token Refund';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1']);
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create1', [
                    'model' => $model,
                ]);
            }
        }}else{

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create1_ta', [
                    'model' => $model,
                ]);
            }
        }
    }
    
    public function actionUpdate1_ta($id)
    {
		$insert_id=0;
        $model = $this->findModel($id);
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }

        if($model->load(Yii::$app->request->post()))
        {
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
        $connection = Yii::$app->getDb();
		if($model->vtype==3){$model->bank_id=$_POST['ref'];}
		if($model->vtype==1){$model->bank_id=$_POST['ref'];}
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          //$model->vno=($result_max['MAX(vno)']+1);
        if ($model->save()) 
        {
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb();
			
			$sqltsa2 = "SELECT * from cconfig where type=19";
                        $config = $conn->createCommand($sqltsa2)->queryOne();
                        $_POST['states-select']=$config['aid'];
			
			$sql3d = "Delete from payment Where pfor=1 AND vid='".$insert_id."'";
                        \Yii::$app->db->createCommand($sql3d)->execute();
			
			
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				$tt=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
					
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount1='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',remarks='".$_POST['narr'.$j]."',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
					$tt=$tt+$_POST['tax1'.$j];	}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
			
					$toinv=0;
					if(isset($_POST['tq']) && $_POST['tq']>0 && isset($_POST['qamount1']) && $_POST['qamount1']>0)
				 	{
				 		$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-($tt))."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			            \Yii::$app->db->createCommand($sql)->execute();
				 		$q=1;
				 		do
				 		{
				 		if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
				 		{
				 			
				 			
				 			$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',pro_id=".$_POST['vid'.$q].", pfor='1',amount='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         		\Yii::$app->db->createCommand($sql)->execute();
				 			$toinv=$toinv+$_POST['qamount'.$q];
				 		    
				 		}							
				 			$q=$q+1;
				 		}while($q < ($_POST['tq']+1));
						
						if($_POST['qamount1']>0 && $toinv!=$model->amount ){
						    
						     $sql1 = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',pro_id='0', pfor='1',amount='".($model->amount-$toinv)."',date='".$model->create_date."',status='1',remarks='Advance Payment',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         		\Yii::$app->db->createCommand($sql1)->execute();
				 			
						    
						}
						
				 	}
				 	else
				 	{
				 	    if($model->amount !==0){
				 	$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         \Yii::$app->db->createCommand($sql)->execute();
			        
			         $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-$tt)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         \Yii::$app->db->createCommand($sql)->execute();
				 	    }
				 	        
				 	    }
					
				
			
				
			        $a='Token Refund';
                    $at='Update';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1']);
        } 
        else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('update1_ta', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('update1_ta', [
                    'model' => $model,
                ]);
            }
        }
            
        }else{

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('update1_ta', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('update1_ta', [
                    'model' => $model,
                ]);
            }
        }
    }
    
    
    public function actionCreate2()
    {
		$insert_id=0;
        $model = new Transaction();
       
        
        if ($model->load(Yii::$app->request->post())) 
        {
             if(isset($_POST['states-select']) && !empty($_POST['states-select']))
            {
                $model->pt_id=$_POST['states-select'];
            }
             if(isset($_POST['states-select1']) && !empty($_POST['states-select1']))
            {
                $model->bank_id=$_POST['states-select1'];
            }
            if(isset($_POST['date']) && !empty($_POST['date']))
            {
                $model->create_date=date("Y-m-d", strtotime($_POST['date']));
            }
            
            if(isset($model->slipdate) && !empty($model->slipdate))
            {
                $model->slipdate=date("Y-m-d", strtotime($model->slipdate));
            }
            $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          $model->vno=($result_max['MAX(vno)']+1);
            if($model->save())
            {
			$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',vid=".$model->id.", pfor='1',amount='".$model->amount."',amount1='0',
			date='".date('Y-m-d')."',status='1',type='1',remarks='OK',createdate='".date('Y-m-d')."'";
			\Yii::$app->db->createCommand($sql)->execute();
			
			
			$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->bank_id."',vid=".$model->id.", pfor='1',amount='0',amount1='".$model->amount."',
			date='".date('Y-m-d')."',status='1',type='1',remarks='OK',createdate='".date('Y-m-d')."'";
			\Yii::$app->db->createCommand($sql)->execute();
			
			
			$insert_id = $model->id;
			
				
			        $a='Deposite Slip';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index2']);
            }
            else
            {
                $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create2', [
                    'model' => $model,
                ]);
            }
            }
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create2', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionCreate1_expensecopy()
    {
		$insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            if(isset($_POST['check'])){
                $model->pt_id=0;
            }
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
             $model->cheque_date=date("Y-m-d", strtotime($_POST['bdate']));
        }
        if($model->load(Yii::$app->request->post()))
        {
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
        if($model->vtype==1){$model->bank_id=$_POST['ref'];}
         
         $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          $model->vno=($result_max['MAX(vno)']+1);
          
          if(!empty($model->remarks)){$model->remarks=str_replace("'","",$model->remarks);}
          
         //echo $model->bank_id;exit;
          $model->pt_id=0;
         if ($model->save()) 
        {
          
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
				  
				  if(empty($_POST['narr'.$j])){$_POST['narr'.$j]='Expense';}else{$_POST['narr'.$j]=str_replace("'","",$_POST['narr'.$j]);}
				 $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['acc1'.$j]."',vid=".$insert_id.", pfor='1',amount='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',
					remarks='".$_POST['narr'.$j]."',createdate='".$model->create_date."'";
			        \Yii::$app->db->createCommand($sql)->execute();
						}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
				if($model->vtype==3 || $model->vtype==4 || $model->vtype==1 || $model->vtype==2)
				{
				
				if($model->pt_id > 0)
				{
				$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount1='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			    \Yii::$app->db->createCommand($sql)->execute();    
                
                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			    \Yii::$app->db->createCommand($sql)->execute();
				}
                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			    \Yii::$app->db->createCommand($sql)->execute();
				}
				
					
				
			        $a='Pay Expense';
                    $at='Create';
                    $u=$insert_id;
                    $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1']);}
        } else {
            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_create', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('create1_expense', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionAdjustment()
    {
        $insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        
        //exit;
        $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          $model->vno=($result_max['MAX(vno)']+1);
        if($model->load(Yii::$app->request->post()) && $model->save()) 
        {
         $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
         and vtype='".$model->vtype."'";
         $result_max = $connection->createCommand($sql_max)->queryOne();
            $model = $this->findModel($model->id);
            $model->vno=($result_max['MAX(vno)']+1);
            $model->save();
            
            $insert_id = $model->id;
            $conn=Yii::$app->getDb(); 
            $ta=0;$tam=0;
            if(isset($_POST['tq']) &&  $_POST['tq'] >0)
            {
                $q = 1;
                $re=0;
                do
                {
                    if(isset($_POST['qamount'.$q]) && isset($_POST['rowid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['rowid'.$q]))
                    {
                        if(isset($_POST['chek']) && $_POST['chek']==1){
                        $sqltsa2 = "SELECT * from cconfig where type=19";
                        $config = $conn->createCommand($sqltsa2)->queryOne();
                        $re=$config['aid'];    
                        }else{$re=$_POST['states-select'];}
                        $amt  = $_POST['qamount'.$q] + $ta;
                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$re."',vid='".$insert_id."',pro_id=".$_POST['rowid'.$q].",
                        pfor='1',amount='".$amt."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',msno='".$_POST['states-select']."',gl=0";
                        \Yii::$app->db->createCommand($sql)->execute();
                        
                        $tam  = $tam+$amt;
                    }
                    $q=$q+1;
                }while($q < ($_POST['tq']+1)); 
            }

            if(isset($_POST['tqs']) && $_POST['tqs'] > 0)
            {
                $q = 1;
                do   
                {
                    if(isset($_POST['qamounts'.$q])  && $_POST['qamounts'.$q]>0 )
                    {
                        
                        $comm1 = Commissionsub::find()->where(['pid' => $_POST['vids'.$q], 'status' => NULL])->all();
                        foreach ($comm1 as $comm) 
                        { 
                            if($comm->amount != '' || $comm->amount != 0)
                            {
                                $com = Commission::find()->where(['id' => $comm->comid])->one();
                                $connection = Yii::$app->db;  
                                $sqltsa2 = "SELECT * from cconfig where type=17";
                                $config = $connection->createCommand($sqltsa2)->queryOne();
                                
                                if($com->id>0)
                                {
                                
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount1='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
        
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                    
                                    $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                }
                            }
                        }
                        
                        $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid=".$_POST['vids'.$q].", pfor='1',
                        amount1='".$_POST['qamounts'.$q]."',date='".$model->create_date."',status='1',remarks='".$_POST['rems'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                        \Yii::$app->db->createCommand($sql2)->execute();
                    }             
                    $q=$q+1;
                }while($q < ($_POST['tqs']+1));
            }
            else if($_POST['states-select1']>0)
            {
                $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid='0', pfor='1',
                amount1='".$tam."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                \Yii::$app->db->createCommand($sql2)->execute();
            }
            
            
            $a='Adjustment';
            $at='Create';
            $u=$insert_id;
           $d = '';
                Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
        
            return $this->redirect(['indexad']);
        } 
        else 
        {
            return $this->render('adjustment', ['model' => $model]);
        }
    }
    
	public function actionUpdateadt($id)
    {
        $insert_id=0;
        $model = $this->findModel($id);
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        
        //exit;
        $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          //$model->vno=($result_max['MAX(vno)']+1);
        if($model->load(Yii::$app->request->post()) && $model->save()) 
        {
            $insert_id = $model->id;
			
			$sql3d = "Delete from payment Where pfor=1 AND vid='".$insert_id."'";
                        \Yii::$app->db->createCommand($sql3d)->execute();
		  $amt=0;	
			
            $conn=Yii::$app->getDb(); 
            $ta=0;$tam=0;
            if(isset($_POST['tq']) &&  $_POST['tq'] >0)
            {
                $q = 1;
                $re=0;
                do
                {
                    if(isset($_POST['qamount'.$q]) && isset($_POST['rowid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['rowid'.$q]))
                    {
                        if(isset($_POST['chek']) && $_POST['chek']==1){
                        $sqltsa2 = "SELECT * from cconfig where type=19";
                        $config = $conn->createCommand($sqltsa2)->queryOne();
                        $re=$config['aid'];    
                        }else{$re=$_POST['states-select'];}
                        $amt  = $_POST['qamount'.$q] + $ta;
                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$re."',vid='".$insert_id."',pro_id=".$_POST['rowid'.$q].",
                        pfor='1',amount='".$amt."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',msno='".$_POST['states-select']."',gl=0";
                        \Yii::$app->db->createCommand($sql)->execute();
                        
                        $tam=$tam+$amt;
                    }
                    $q=$q+1;
                }while($q < ($_POST['tq']+1)); 
            }

            if(isset($_POST['tqs']) && $_POST['tqs'] > 0)
            {
                $q = 1;
                do   
                {
                    if(isset($_POST['qamounts'.$q])  && $_POST['qamounts'.$q]>0 )
                    {
                        
                        $comm1 = Commissionsub::find()->where(['pid' => $_POST['vids'.$q], 'status' => NULL])->all();
                        foreach ($comm1 as $comm) 
                        { 
                            if($comm->amount != '' || $comm->amount != 0)
                            {
                                $com = Commission::find()->where(['id' => $comm->comid])->one();
                                $connection = Yii::$app->db;  
                                $sqltsa2 = "SELECT * from cconfig where type=17";
                                $config = $connection->createCommand($sqltsa2)->queryOne();
                                
                                if($com->id>0)
                                {
                                
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount1='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
        
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                    
                                    $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                }
                            }
                        }
                        
                        $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid=".$_POST['vids'.$q].", pfor='1',
                        amount1='".$_POST['qamounts'.$q]."',date='".$model->create_date."',status='1',remarks='".$_POST['rems'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                        \Yii::$app->db->createCommand($sql2)->execute();
                    }             
                    $q=$q+1;
                }while($q < ($_POST['tqs']+1));
            }
            else if($_POST['states-select1']>0)
            {
                $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid='0', pfor='1',
                amount1='".$tam."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                \Yii::$app->db->createCommand($sql2)->execute();
            }
            $a='Adjustment';
            $at='Update';
            $u=$insert_id;
            $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
        
            return $this->redirect(['indexad']);
        } 
        else 
        {
            return $this->render('updateadt', ['model' => $model]);
        }
    }
    
	
	public function actionAdjustment_pay()
    {
        $insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        //exit;
        $connection = Yii::$app->getDb();
        
        $sql_max = "SELECT MAX(vno) from transaction";
        $result_max = $connection->createCommand($sql_max)->queryOne();
        
        $model->vno=($result_max['MAX(vno)']+1);
        if($model->load(Yii::$app->request->post())) 
        {
            if ( isset ( $_POST['is_discount_voucher'] ) ) {
                $model->ttype = 7;
            }
            if ( $model->save() )
            {
                $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
                and vtype='".$model->vtype."'";
                $result_max = $connection->createCommand($sql_max)->queryOne();
                
                $model = $this->findModel($model->id);
                $model->vno=($result_max['MAX(vno)']+1);
                $model->save();
                
                $insert_id = $model->id;
                $conn=Yii::$app->getDb(); 
                
    			
    			$amt=0;			
                if(isset($_POST['tqs']) && $_POST['tqs'] > 0)
                {
                    $q = 1;
                    do   
                    {
                        if(isset($_POST['qamounts'.$q])  && $_POST['qamounts'.$q]>0 )
                        {
                            $comm1 = Commissionsub::find()->where(['pid' => $_POST['vids'.$q], 'status' => NULL])->all();
                            foreach ($comm1 as $comm) 
                            { 
                                if($comm->amount != '' || $comm->amount != 0)
                                {
                                    $com = Commission::find()->where(['id' => $comm->comid])->one();
                                    $connection = Yii::$app->db;  
                                    $sqltsa2 = "SELECT * from cconfig where type=17";
                                    $config = $connection->createCommand($sqltsa2)->queryOne();
                                    
                                    if($com->id>0)
                                    {
                                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                        amount1='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                        createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                        \Yii::$app->db->createCommand($sql)->execute();
            
                                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                        amount='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                        createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                        \Yii::$app->db->createCommand($sql)->execute();
                                        
                                        $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";
                                        \Yii::$app->db->createCommand($sql)->execute();
                                    }
                                }
                            }
                            
                            $status=0;
                            if($_POST['status'.$q]==1){$status=1;}
                            $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid=".$_POST['vids'.$q].", pfor='1',
                            amount1='".$_POST['qamounts'.$q]."',date='".$model->create_date."',status='1',remarks='".$_POST['rems'.$q]."',
                            createdate='".date('Y-m-d')."',type='0',jvid='".$status."',gl=0";
                            \Yii::$app->db->createCommand($sql2)->execute();
    						
    					    $amt=$amt+$_POST['qamounts'.$q];
                        }             
                        $q=$q+1;
                    }while($q < ($_POST['tqs']+1));
    				
    				$re=$_POST['states-select'];
    				
    				$sql3 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$re."',vid='".$insert_id."',pro_id='0',
                    pfor='1',amount='".$amt."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                    \Yii::$app->db->createCommand($sql3)->execute();
                }
                
                // $a='Adjust Payment';
                // $at='Create';
                // $u=$insert_id;
                // $d = '';
                // Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
            
			    
                $a='14'; // Adjust Payment ID in system_activities
                $at='Create';
                $u=$model->id;
                
                $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$_POST['states-select1']."' AND type=1 ")->queryOne();
                $details = "VoucherDate: ".$model->create_date.",ReceiptType: Adjustment Voucher, Receipt#: ".$model->receipt.", Amount: ".$model->amount.", Narration: ".$model->remarks." ";
                Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$memberplot['ref']);
            
                return $this->redirect(['indexad']);
            }
        } 
        else 
        {
            return $this->render('adjustment_pay', ['model' => $model]);
        }
    }
    
	
	public function actionAdjustment1()
    {
        $insert_id=0;
        $model = new Transaction();
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        
        //exit;
        $connection = Yii::$app->getDb();
    
         $sql_max = "SELECT MAX(vno) from transaction";
         $result_max = $connection->createCommand($sql_max)->queryOne();
          
          $model->vno=($result_max['MAX(vno)']+1);
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
        {
            
             
         $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
         and vtype='".$model->vtype."'";
         $result_max = $connection->createCommand($sql_max)->queryOne();
            $model = $this->findModel($model->id);
            $model->vno=($result_max['MAX(vno)']+1);
            $model->save();
            $insert_id = $model->id;
            $conn=Yii::$app->getDb(); 
            $ta=0;
            if(isset($_POST['tq']) &&  $_POST['tq'] >0)
            {
                $q = 1;
                do
                {
                    if(isset($_POST['qamount'.$q]) && isset($_POST['rowid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['rowid'.$q]))
                    {
                        $amt  = $_POST['qamount'.$q] + $ta;
                        $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select']."',vid='".$insert_id."',pro_id=".$_POST['rowid'.$q].",
                        pfor='1',amount='".$amt."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                        \Yii::$app->db->createCommand($sql)->execute();
                        if($_POST['oam'.$q]==$amt){
                        $sql = "Update payment SET sid='0' where id='".$_POST['rowid'.$q]."'";
                        \Yii::$app->db->createCommand($sql)->execute();
                        }else{
                                $p = "SELECT * from payment where id='".$_POST['rowid'.$q]."'";
                                $p1 = $connection->createCommand($p)->queryOne();
                                
                         $sql2 = "Update payment SET amount1='".$amt."', sid=0 where id='".$_POST['rowid'.$q]."'";  
                        \Yii::$app->db->createCommand($sql2)->execute();

                        $sql1 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select']."',vid='".$p1['vid']."',sid=".$p1['sid'].",
                        pfor='1',amount1='".($_POST['oam'.$q]-$amt)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                       \Yii::$app->db->createCommand($sql1)->execute();
                        }
                            
                        }
                    $q=$q+1;
                }while($q < ($_POST['tq']+1)); 
            }

            if(isset($_POST['tqs']) && $_POST['tqs'] > 0)
            {
                $q = 1;
                do   
                {
                    if(isset($_POST['qamounts'.$q])  && $_POST['qamounts'.$q]>0 )
                    {
                        $comm1 = Commissionsub::find()->where(['pid' => $_POST['vids'.$q], 'status' => NULL])->all();
                        foreach ($comm1 as $comm) 
                        { 
                            if($comm->amount != '' || $comm->amount != 0)
                            {
                                $com = Commission::find()->where(['id' => $comm->comid])->one();
                                $connection = Yii::$app->db;  
                                $sqltsa2 = "SELECT * from cconfig where type=17";
                                $config = $connection->createCommand($sqltsa2)->queryOne();
                                
                                if($com->id>0)
                                {
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount1='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
        
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                    
                                    $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                }
                            }
                        }
                        
                        $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid=".$_POST['vids'.$q].", pfor='1',
                        amount1='".$_POST['qamounts'.$q]."',date='".$model->create_date."',status='1',remarks='".$_POST['rems'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                        \Yii::$app->db->createCommand($sql2)->execute();
                    }          
                    $q=$q+1;
                }while($q < ($_POST['tqs']+1));
            }else{
                
                $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid=0, pfor='1',
                        amount1='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                        \Yii::$app->db->createCommand($sql2)->execute();
            }
            
            $a='Transfer Payment';
            $at='Create';
            $u=$insert_id;
            $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
        
            return $this->redirect(['indexad']);
        } 
        else 
        {
            return $this->render('adjustment1', ['model' => $model]);
        }
    }
    
    
    
	public function actionAdjust_advance(){
	    $conn = Yii::$app->getDb();
	    	if(isset($_REQUEST['vvid']) && $_REQUEST['qamount']>0){
					  
					  	$v= "SELECT * FROM payment where id = '".$_REQUEST['vvid']."'";
	                    $ro = $conn->createCommand($v)->queryOne();
	                    
	                    $vp= "SELECT sum(amount) FROM payment where pro_id = '".$_REQUEST['vvid']."'";
	                    $rop = $conn->createCommand($vp)->queryOne();
	                    
					  	$adv= "SELECT * FROM payment where referanceid='".$ro['referanceid']."' and (pro_id = 0 or pro_id is NULL) and amount>0";
	                    $vresult = $conn->createCommand($adv)->queryOne();
	                    //print_r($advresult);exit;
	                    $qmrem=0;
	                   $invam=$ro['amount1']-$rop['amount'];
	                        $ins=0;
	                       
	                    if($invam== $_REQUEST['qamount'] or $invam >  $_REQUEST['qamount']){$ins=$_REQUEST['qamount'];}
	                    if($invam <  $_REQUEST['qamount']){$ins=$invam; $qmrem=$_REQUEST['qamount']-$invam;}
	                   
	                    $sql = "update payment SET amount='".($ins)."',pro_id='".$_REQUEST['vvid']."' where id='".$vresult['id']."'";
			            \Yii::$app->db->createCommand($sql)->execute(); 
	                     if($qmrem>0){
    	                 $sql12 = "INSERT into payment SET referanceid='".$ro['referanceid']."',vid=".$ro['vid'].", pfor='1',amount='".($qmrem)."',date='".date('Y-m-d')."',status='1',remarks='".$ro['remarks']."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
    			         \Yii::$app->db->createCommand($sql12)->execute();   
	                          
	                      }	//echo 123;exit;
	                    
					    
					    
					}
	    
	}
	public function actionPetty()
    {
		$i=0;
		 if(isset($_POST['totalexp']) ){
		do{	
		$i=$i+1;$type='';
		if($_POST['type'.$i]==1){$type='exp';}else{$type='exp1';}	
		if(($_POST['da'.$i]!=='' && $_POST['amountd'.$i]!=='') ){
			$sql = "INSERT INTO `payment`(`referanceid`, `pfor`, `vid`, `amount`, `tax`, `date`, `remarks`, `type`, `status`, `createdate`) value('".$_POST['da'.$i]."','".$type."','0','".$_POST['amountd'.$i]."','0','".date('Y-m-d')."','".$_POST['remarks'.$i]."','1','1','".date('Y-m-d')."')";
			\Yii::$app->db->createCommand($sql)->execute();	
        $a='Petty Cash';
                    $at='Create';
                    $u=Yii::$app->db->getLastInsertID();
                   $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);

    }		
		}while($_POST['totalexp'] < $i);

  
	return $this->redirect(Yii::$app->urlManager->baseUrl.'/index.php?r=transaction/pettylist');
		 }
else{
		return $this->render('petty');}
	}
	
	public function actionUpdate($id)
    {
        $model = $this->findModel($id);
         if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        $cash_id=$model->bank_id;
        
        if($model->load(Yii::$app->request->post()))
        {
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
        if($model->vtype==3){$model->bank_id=$_POST['ref'];}
        if ($model->save()) 
        {
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				$tt=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
					
					if(isset($_POST['tu'.$j]) && $_POST['tu'.$j]>0)
				 			{
				 			    
            					$sql = "Update payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',date='".$model->create_date."',amount='".$_POST['tax1'.$j]."' Where id='".$_POST['tu'.$j]."'";
            			        \Yii::$app->db->createCommand($sql)->execute();
				 			}
				 			else
				 			{
				 			    
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount='".$_POST['tax1'.$j]."',date='".date('Y-m-d')."',status='1',type='1',remarks='Tax Paid',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 			}
					$tt=$tt+$_POST['tax1'.$j];	}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
			
					$toinv=0;
						if(isset($_POST['tq']) && $_POST['tq'] > 0)
 					{

$sql3d = "Delete from payment Where pfor=1 AND vid='".$insert_id."'";
            \Yii::$app->db->createCommand($sql3d)->execute();
         

            if($model->amount > 0){
$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.",sid=0, pfor='1',amount='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
              \Yii::$app->db->createCommand($sql)->execute();
// /// echo
$q=1;
		do  
           {
           if(isset($_POST['qamount'.$q]) && isset($_POST['vid'.$q]) && $_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
						{
              // echo "string3"; 
         
            $comm1 = Commissionsub::find()->where(['pid' => $_POST['vid'.$q], 'status' => NULL])->all();
           
            foreach ($comm1 as $comm) { 
            if($comm->amount != '' || $comm->amount != 0){
// echo "string";
              $com = Commission::find()->where(['id' => $comm->comid])->one();
    $connection = Yii::$app->db;  

              $sqltsa2 = "SELECT * from cconfig where type=17";
                        $config = $connection->createCommand($sqltsa2)->queryOne();
             

              $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vid'.$q].", pfor='10',amount1='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0'";


                  \Yii::$app->db->createCommand($sql)->execute();

              $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vid'.$q].", pfor='10',amount='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0'";


                  \Yii::$app->db->createCommand($sql)->execute();

                  $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";


                  \Yii::$app->db->createCommand($sql)->execute();

            }
          }

							$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',vid='".$insert_id."',sid=".$_POST['vid'.$q].", pfor='1',amount1='".$_POST['qamount'.$q]."',date='".$model->create_date."',status='1',remarks='".$_POST['rem'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='0'";


			        		\Yii::$app->db->createCommand($sql)->execute();
							}							
							$q=$q+1;
						}while($q < ($_POST['tq']+1));
						
						
					} }
				 	else
				 	{
				 	    if($model->amount !==0){
				 	$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount1='".($model->amount+$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         \Yii::$app->db->createCommand($sql)->execute();
			        
			         $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount-$tt)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			         \Yii::$app->db->createCommand($sql)->execute();
				 	    }
				 	        
				 	    }
					
			
				
			        $a='Recieve Installments';
                    $at='Update';
                    $u=$insert_id;
                    $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['ins_recieved']);
        } } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
	

public function actionUpdate_ins1($id)
    {
        $model = $this->findModel($id);
         if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        $cash_id=$model->bank_id;
        
        if($model->load(Yii::$app->request->post()))
        {
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
        if($model->vtype==3){$model->bank_id=$_POST['ref'];}
        if ($model->save()) 
        {
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				
				$sql = "Update payment SET date='".$model->create_date."',remarks='".$model->remarks."',salecenter='".$model->salecenter."',referanceid='". $model->pt_id."',amount1='".$model->amount."' Where vid='".$model->id."' AND pfor=1 AND amount1>0";
			    \Yii::$app->db->createCommand($sql)->execute();
				
				$sql = "Update payment SET date='".$model->create_date."',remarks='".$model->remarks."',salecenter='".$model->salecenter."',referanceid='". $ref."',amount='".$model->amount."' Where vid='".$model->id."' AND pfor=1 AND amount>0";
			    \Yii::$app->db->createCommand($sql)->execute();
				
				
			        $a='Receive Payment';
                    $at='Update';
                    $u=$insert_id;
                   $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index']);
        } } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update_ins', [
                    'model' => $model,
                ]);
            }
        }
    }
		
	 
	
    
    public function actionIndexad()
    {	
		if(isset($_REQUEST['page']) && $_REQUEST['page']>0)
		{
		    function findper($search, $ref, $value)
            {
                $keys=array();
                foreach($search as $key)
                {
                    if($key[$ref]==$value) 
                    { 
                        return $key;
                    }
                }
                 
            }
        
            $pay = findper($_SESSION['user_perm_array'], 'module_id', 107);  
            $new=false;
            $save=false;
            $remove=false;
            if($pay['new']==1){$new=true;}
            if($pay['save']==1){$save=true;}
            if($pay['remove']==1){$remove=true;}
		    
		    
			$connection = Yii::$app->getDb();
			
			$page = 1;
			if(!empty($_REQUEST["page"])) 
			{
				$page = $_REQUEST["page"];
			}
			
			$start = ($page-1)*20;
			if($start < 0) $start = 0;
			
			$where='';
			if($_SESSION["user_array"]["usertype"]==2)
		    {
		        $where.=" AND transaction.user_id='".$_SESSION["user_array"]["id"]."'";        
		    }
			if(isset($_POST['date']) && !empty($_POST['date']))
			{
			    $where.=" AND transaction.create_date='".$_POST['date']."'";
			}
			if(isset($_POST['vno']) && !empty($_POST['vno']))
			{
			    $where.=" AND transaction.receipt='".$_POST['vno']."'";
			}
			if(isset($_POST['msno']) && !empty($_POST['msno']))
			{
			    $where.=" AND memberplot.plotno Like '%".$_POST['msno']."%'";
			}
			if(isset($_POST['customer']) && !empty($_POST['customer']))
			{
			    $where.=" AND members.name Like '%".$_POST['customer']."%'";
			}
			if(isset($_POST['dealer']) && !empty($_POST['dealer']))
			{
			    $where.=" AND to_id.id='".$_POST['dealer']."'";
			}
			if(isset($_POST['block']) && !empty($_POST['block']))
			{
			    $where.=" AND plots.sector='".$_POST['block']."'";
			}
			if(isset($_POST['floor']) && !empty($_POST['floor']))
			{
			    $where.=" AND plots.street_id='".$_POST['floor']."'";
			}
			if(isset($_POST['unit']) && !empty($_POST['unit']))
			{
			    $where.=" AND plots.plot_detail_address='".$_POST['unit']."'";
			}
		    $sqlt = "SELECT * from transaction
			Left Join accounts from_id ON (transaction.pt_id=from_id.id)
			Left Join memberplot ON (from_id.ref=memberplot.id AND from_id.type=1)
			Left Join plots ON (plots.id=memberplot.plot_id) 
			Left Join members ON (members.id=memberplot.member_id)
			Where transaction.status_type In (6,7,8) $where Group By transaction.id" ;
			$resultt = $connection->createCommand($sqlt)->queryAll();

			
		    $sql = "SELECT transaction.create_date,transaction.receipt,transaction.amount,from_id.name as fname,transaction.status_type,transaction.vtype,transaction.id as tid, 
			plots.plot_detail_address,plotno,memberplot.status as mp_status from transaction
			Left Join accounts from_id ON (transaction.pt_id=from_id.id)
			Left Join memberplot ON (from_id.ref=memberplot.id AND from_id.type=1)
			Left Join plots ON (plots.id=memberplot.plot_id) 
			Left Join members ON (members.id=memberplot.member_id)
			Where transaction.status_type In (6,7,8) $where Group By transaction.id  Order By transaction.id DESC Limit $start,20";
			$result = $connection->createCommand($sql)->queryAll();
			
			
			$_REQUEST["rowcount"] = count($resultt);
			
		    
		
			$paginationlink = "index.php?r=transaction/indexad&page=";	
			$pagination_setting = "all-links";
			$perpageresult = Yii::$app->mycomponent->Pagination($_REQUEST["rowcount"], $paginationlink);
			
			$output = '';
			foreach($result as $row) 
			{
				// $type='';ins
				// if($row['status_type']==6){$type="Token";}
				// if($row['status_type']==7){$type="Adj Payment";}
				// if($row['status_type']==8){$type="Transfer";}
				
				echo $sql_to = "SELECT accounts.name as tname from payment
    			Left Join accounts ON (payment.referanceid=accounts.id)
    			Where payment.vid='".$row['tid']."' AND pfor=1 AND amount!=''";
    			$result_to = $connection->createCommand($sql_to)->queryOne();
			?>
<tr>
    <td align="center" style="width: 8%;"><?php echo date("d-m-Y", strtotime($row['create_date'])); ?></td>
    <td align="center" style="width: 10%;"><?php echo $row['receipt'] ?></td>
    <td align="center" style="width: 8%;">
        <?php echo $row['plotno']; ?>
    </td>
    <td align="center" style="width: 9%;"><?php echo number_format($row['amount'],0) ?></td>
    <td align="center" style="width: 20%;"><?php echo $result_to['tname'] ?></td>
    <td align="center" style="width: 20%;"><?php echo $row['fname'] ?></td>
    <td class="center" style="width: 6%;">
        <?php if($row['mp_status'] == 'Cancel') { ?>
        <?php if($row['status_type'] == 7 && ($row->isApp == 0 || $_SESSION['user_array']['user_level'] == 1) && $save) { ?>
        <!-- Disabled Edit Icon with Alert -->
        <span class="tooltip-error ajaxlink" data-rel="tooltip" title="This transaction is cancelled."
            onclick="alert('This  is Merged File Kindly Revert The File First For Updation.'); return false;">
            <i class="ace-icon fa fa-pencil bigger-120" style="color: gray;"></i>
        </span>
        <?php } ?>
        <?php if($row['status_type'] == 6 && ($row->isApp == 0 || $_SESSION['user_array']['user_level'] == 1) && $save) { ?>
        <!-- Disabled Edit Icon with Alert -->
        <span class="tooltip-error ajaxlink" data-rel="tooltip" title="This transaction is cancelled."
            onclick="alert('This  is Merged File Kindly Revert The File First For Updation.'); return false;">
            <i class="ace-icon fa fa-pencil bigger-120" style="color: gray;"></i>
        </span>
        <?php } ?>

        <?php if($row['status_type'] != 8 && ($row->isApp == 0 || $_SESSION['user_array']['user_level'] == 1) && $remove) { ?>
        <!-- Disabled Delete Icon with Alert -->
        <span class="tooltip-error deletelink" data-rel="tooltip" title="This transaction is cancelled."
            onclick="alert('This  is Merged File Kindly Revert The File First For Deletion.'); return false;">
            <i class="ace-icon fa fa-trash-o bigger-120" style="color: gray;"></i>
        </span>
        <?php } ?>
        <?php } else { ?>
        <?php if($row['status_type'] == 7 && ($row->isApp == 0 || $_SESSION['user_array']['user_level'] == 1) && $save) { ?>
        <!-- Active Edit Link -->
        <a data-original-title="Edit"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/updatead&id=<?php echo $row['tid']; ?>"
            class="tooltip-error ajaxlink" data-rel="tooltip" title="">
            <span class="blue"><i class="ace-icon fa fa-pencil bigger-120"></i></span>
        </a>
        <?php } ?>
        <?php if($row['status_type'] == 6 && ($row->isApp == 0 || $_SESSION['user_array']['user_level'] == 1) && $save) { ?>
        <!-- Active Edit Link -->
        <a data-original-title="Edit"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/updateadt&id=<?php echo $row['tid']; ?>"
            class="tooltip-error ajaxlink" data-rel="tooltip" title="">
            <span class="blue"><i class="ace-icon fa fa-pencil bigger-120"></i></span>
        </a>
        <?php } ?>

        <?php if($row['status_type'] != 8 && ($row->isApp == 0 || $_SESSION['user_array']['user_level'] == 1) && $remove) { ?>
        <!-- Active Delete Link -->
        <a onclick="return confirm('Are you sure?')" data-original-title="Delete"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/delete3&id=<?php echo $row['tid']; ?>"
            class="tooltip-error deletelink" data-rel="tooltip" title="">
            <span class="red"><i class="ace-icon fa fa-trash-o bigger-120"></i></span>
        </a>
        <?php } ?>
        <?php } ?>



        <?php if($row['vtype']==1 or $row['vtype']==3 or $row['vtype']==5){ ?>
        <a target="_blank" data-original-title="Print"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/adjprint1&id=<?php echo $row['tid']; ?>"
            class="tooltip-error deletelink" data-rel="tooltip" title=""> <span class="green"> <i
                    class="ace-icon fa fa-print"></i> </span> </a>
        <?php } ?>
        <?php if($row['vtype']==2 or $row['vtype']==4){ ?>
        <a target="_blank" data-original-title="Print"
            href="<?php echo Yii::$app->urlManager->baseUrl; ?>/index.php?r=/transaction/adjprint2&id=<?php echo $row['tid']; ?>"
            class="tooltip-error deletelink" data-rel="tooltip" title=""> <span class="green"> <i
                    class="ace-icon fa fa-print"></i> </span> </a>
        <?php } ?>
    </td>
</tr>
<?php	
			}
			if(!empty($perpageresult)) 
			{
			?>
<input type="hidden" name="rowcount" id="rowcount" value="<?php echo $_REQUEST["rowcount"]; ?>" />
<tr style="height: 44px;">
    <td colspan="6">
        <div style="margin-top: 10px;"><?php echo $perpageresult; ?></div>
    </td>
</tr>
<?php
			}
		}
		else
		{
			return $this->render('index1_chk');
		}
	}
	
    
    // public function actionIndexad()
    // {

    //     $request = Yii::$app->request;
        
    //     $searchModel = new TransactionSearch();

    //     $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    //     $dataProvider->query->orderBy(['id' => SORT_DESC,]);
    //     if($_SESSION["user_array"]["usertype"]==2)
    //     {
    //         $dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
    //     }
    //     $dataProvider->query->andFilterWhere(['transaction.vtype'=>5]);
    //     $dataProvider->pagination->pageSize=$request->get("pagesize",20);
    //     if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
    //     $dataProvider->pagination->page=($_REQUEST['page']-1);
    //     if($request->isAjax)
    //     {
    //         return $this->renderPartial('_index', [
    //             'searchModel' => $searchModel,
    //             'dataProvider' => $dataProvider,
    //         ]);
    //     }
    //     else
    //     {
    //         return $this->render('indexad', [
    //             'searchModel' => $searchModel,
    //             'dataProvider' => $dataProvider,
    //         ]);
    //     }
    // }
    public function actionIndex1_salary()
    {

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['id' => SORT_DESC,]);
        if($_SESSION["user_array"]["usertype"]==2)
		{
		    $dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
		}
		$dataProvider->query->andFilterWhere(['transaction.vtype'=>1]);
		$dataProvider->query->orFilterWhere(['transaction.vtype'=>2]);
		$dataProvider->query->andFilterWhere(['transaction.status_type'=>[2,4]]);
        $dataProvider->pagination->pageSize=$request->get("pagesize",20);
         if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
		$dataProvider->pagination->page=($_REQUEST['page']-1);
		
		$request1 = Yii::$app->request;
        
        $searchModel1 = new TransactionSearch();
        $dataProvider1 = $searchModel1->search(Yii::$app->request->queryParams);
        $dataProvider1->query->Where('jv=1 and jv_ref=0 ');
	//	$dataProvider->query->orWhere('');
        $dataProvider1->pagination->pageSize=$request1->get("pagesize",20);
        $dataProvider1->pagination->page=$request1->get("pageno",0);
		

        if($request->isAjax)
        {
            return $this->renderPartial('_index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else
        {
            return $this->render('index1_salary', [
                'searchModel1' => $searchModel1,
                'dataProvider1' => $dataProvider1,
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    public function actionIndexmr()
    {

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$dataProvider->query->orderBy(['id' => SORT_DESC,]);
        if($_SESSION["user_array"]["usertype"]==2)
    {
    $dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
    }
    
    $dataProvider->query->andFilterWhere(['transaction.ver'=>1]);
        $dataProvider->pagination->pageSize=$request->get("pagesize",20);
         if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
    $dataProvider->pagination->page=($_REQUEST['page']-1);
    
    $request1 = Yii::$app->request;
        
        $searchModel1 = new TransactionSearch();
        $dataProvider1 = $searchModel1->search(Yii::$app->request->queryParams);
        $dataProvider1->query->Where('jv=1 and jv_ref=0 ');
  //  $dataProvider->query->orWhere('');
        $dataProvider1->pagination->pageSize=$request1->get("pagesize",20);
        $dataProvider1->pagination->page=$request1->get("pageno",0);
    

    
            return $this->render('indexmr', [
                'searchModel1' => $searchModel1,
                'dataProvider1' => $dataProvider1,
        'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        
    }
    public function actionIndex2()
    {

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['id' => SORT_DESC,]);
        if($_SESSION["user_array"]["usertype"]==2)
		{
		$dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
		}
		$dataProvider->query->andFilterWhere(['transaction.vtype'=>5]);
        $dataProvider->pagination->pageSize=$request->get("pagesize",20);
         if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
		$dataProvider->pagination->page=($_REQUEST['page']-1);
		
		$request1 = Yii::$app->request;
        
        $searchModel1 = new TransactionSearch();
        $dataProvider1 = $searchModel1->search(Yii::$app->request->queryParams);
        $dataProvider1->query->Where('jv=1 and jv_ref=0 ');
	//	$dataProvider->query->orWhere('');
        $dataProvider1->pagination->pageSize=$request1->get("pagesize",20);
        $dataProvider1->pagination->page=$request1->get("pageno",0);
		

        if($request->isAjax)
        {
            return $this->renderPartial('_index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else
        {
            return $this->render('index2', [
                'searchModel1' => $searchModel1,
                'dataProvider1' => $dataProvider1,
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    
    
    public function actionUpdate1excopy($id)
    {
        $model = $this->findModel($id);
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        $cash_id=$model->bank_id;
        
        if($model->load(Yii::$app->request->post()))
        {
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
         if($model->vtype==1){$model->bank_id=$_POST['ref'];}
         if(!empty($model->remarks)){$model->remarks=str_replace("'","",$model->remarks);}
         $model->pt_id=0;
        if ($model->save()) 
        { 
				
				
				$insert_id = $model->id;
				$sqld = "Delete from  payment where vid=".$insert_id." AND pfor='1'";
						\Yii::$app->db->createCommand($sqld)->execute();
				
				
			$conn=Yii::$app->getDb(); 
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
				  
				  if(empty($_POST['narr'.$j])){$_POST['narr'.$j]='Expense';}else{$_POST['narr'.$j]=str_replace("'","",$_POST['narr'.$j]);}
				 $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['acc1'.$j]."',vid=".$insert_id.", pfor='1',amount='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',
					remarks='".$_POST['narr'.$j]."',createdate='".$model->create_date."'";
			        \Yii::$app->db->createCommand($sql)->execute();
						}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
				if($model->vtype==3 || $model->vtype==4 || $model->vtype==1 || $model->vtype==2)
				{
				
				if($model->pt_id > 0)
				{
				$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount1='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			    \Yii::$app->db->createCommand($sql)->execute();    
                
                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$_POST['states-select']."',vid=".$insert_id.", pfor='1',amount='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			    \Yii::$app->db->createCommand($sql)->execute();
				}
                $sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			    \Yii::$app->db->createCommand($sql)->execute();
				}
			
				
			        $a='Pay Expense';
                    $at='Update';
                    $u=$insert_id;
                    $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1']);
        } 
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update1ex', [
                    'model' => $model,
                ]);
            }
        }
    }
	
	
   
    public function actionMultipleupdate($id)
    {
        $model = $this->findModel($id);
        $model = $this->findModel($_REQUEST['id']);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          $insert_id = $model->id;
      $conn=Yii::$app->getDb(); 
      $sqlt2 = "SELECT * from cconfig where type=15";
      $resultt2 = $conn->createCommand($sqlt2)->queryOne();
      if($model->vtype==1 || $model->vtype==3){$ref=$resultt2['aid'];}
      if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
        $ta=0;
        if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
        $j=1;
        do{
        if(isset($_POST['acc1'.$j])){
          $connection = Yii::$app->getDb();
          $status=1;
          $sql= "SELECT * FROM taxdefination where id='".$_POST['acc1'.$j]."'";
                  $result = $conn->createCommand($sql)->queryOne();
          $tac=0;$tad=0;
          if($model->vtype==1 or $model->vtype==2){$tac=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
          if($model->vtype==3 or $model->vtype==4){$tad=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
          
          $sql = "INSERT into payment SET referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount='".$tad."',amount1='".$tac."',date='".date('Y-m-d')."',status='1',type='1',remarks='OK',createdate='".date('Y-m-d')."'";
              \Yii::$app->db->createCommand($sql)->execute();
            }
        $j=$j+1;
        }while($j < ($_POST['totalexp1']+1));
        } 
        
     
        
        if($model->vtype==3 || $model->vtype==4)
        { 
          if(isset($_POST['tq']) && $_POST['tq']>0)
          {
            $q=1;
            do
            {
            if($_POST['qamount'.$q]>0 && !empty($_POST['vid'.$q]))
            {
              $sql = "Update payment SET amount1='".$_POST['qamount'.$q]."' where referanceid='".$model->pt_id."' AND vid=".$insert_id." AND pfor='1' AND  sid='".$_POST['vid'.$q]."'";
                  \Yii::$app->db->createCommand($sql)->execute();
            }
                            
              $q=$q+1;
            }while($q < ($_POST['tq']+1));
            
          }     
              
          $sql1 = "Update payment SET referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0' where referanceid='".$ref."' AND vid=".$insert_id." AND pfor='1'";
                  \Yii::$app->db->createCommand($sql1)->execute();
                  
                  $sql12 = "Update payment SET referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0' where referanceid='".$model->pt_id."' AND vid=".$insert_id." AND pfor='1'";
                  \Yii::$app->db->createCommand($sql12)->execute();
                  
        }
        
        if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0){
        $i=1;
        do{
         if(isset($_POST['acc'.$i]) and isset($_POST['acc'.$i])=='on')
         {
          $connection = Yii::$app->getDb();
          $status=1;
          
          $sql = "INSERT into payment SET referanceid='".$_POST['acc'.$i]."',vid=".$insert_id.", pfor='1',amount='".($_POST['dam'.$i])."',date='".date('Y-m-d')."',status='1',remarks='".$_POST['details'.$i]."',createdate='".date('Y-m-d')."'";
              \Yii::$app->db->createCommand($sql)->execute();
         }
        $i=$i+1;
        }while($i < ($_POST['totalexp']+1));
        }
                    $a='multireceipt';
                    $at='Update';
                    $u=$insert_id;
                    $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
      return $this->redirect(['indexmr']);
        } else {

        

         
                return $this->render('multireceipt', [
                    'model' => $model,
                ]);
            
        }
    }
     public function actionUpdatead($id)
    {
      $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			$insert_id = $model->id;
            $conn=Yii::$app->getDb();
			
          $sql3d = "Delete from payment Where pfor=1 AND vid='".$insert_id."'";
                        \Yii::$app->db->createCommand($sql3d)->execute();
		  $amt=0;			
            if(isset($_POST['tqs']) && $_POST['tqs'] > 0)
            {
                $q = 1;
                do   
                {
                    if(isset($_POST['qamounts'.$q])  && $_POST['qamounts'.$q]>0 )
                    {
                        
                        $comm1 = Commissionsub::find()->where(['pid' => $_POST['vids'.$q], 'status' => NULL])->all();
                        foreach ($comm1 as $comm) 
                        { 
                            if($comm->amount != '' || $comm->amount != 0)
                            {
                                $com = Commission::find()->where(['id' => $comm->comid])->one();
                                $connection = Yii::$app->db;  
                                $sqltsa2 = "SELECT * from cconfig where type=17";
                                $config = $connection->createCommand($sqltsa2)->queryOne();
                                
                                if($com->id>0)
                                {
                                
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$com->mid."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount1='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
        
                                    $sql = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$config['aid']."',vid='".$com->id."',sid=".$_POST['vids'.$q].", pfor='10',
                                    amount='".$comm->amount."',date='".$model->create_date."',status='1',remarks='Commission on ".$_POST['rems'.$q]."',
                                    createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                    
                                    $sql = "UPDATE commission_sub SET status='1' WHERE id = '".$comm->id."'";
                                    \Yii::$app->db->createCommand($sql)->execute();
                                }
                            }
                        }
                        
                        $status=0;
                        if($_POST['status'.$q]==1){$status=1;}
                        
                        $sql2 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$_POST['states-select1']."',vid='".$insert_id."',sid=".$_POST['vids'.$q].", pfor='1',
                        amount1='".$_POST['qamounts'.$q]."',date='".$model->create_date."',status='1',remarks='".$_POST['rems'.$q]."',createdate='".date('Y-m-d')."',type='0',jvid='".$status."',gl=0";
                        \Yii::$app->db->createCommand($sql2)->execute();
						
					$amt=$amt+$_POST['qamounts'.$q];
                    }             
                    $q=$q+1;
                }while($q < ($_POST['tqs']+1));
				
				$re=$_POST['states-select'];
				
				$sql3 = "INSERT into payment SET salecenter='".$model->salecenter."',branch_id='".$model->branch_id."',referanceid='".$re."',vid='".$insert_id."',pro_id='0',
                        pfor='1',amount='".$amt."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0',gl=0";
                        \Yii::$app->db->createCommand($sql3)->execute();
				
            }
			
// 			$a='Adjust Payment';
//          $at='Update';
//          $u=$insert_id;//$insert_id;
//          $d = '';
//          Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
            
            
			    
            $a='14'; // Adjust Payment ID in system_activities
            $at='Update';
            $u=$model->id;
            
            $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$_POST['states-select1']."' AND type=1 ")->queryOne();
            $details = "VoucherDate: ".$model->create_date.",ReceiptType: Adjustment Voucher, Receipt#: ".$model->receipt.", Amount: ".$model->amount.", Narration: ".$model->remarks." ";
            Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$memberplot['ref']);
        
		  return $this->redirect(['indexad']);
		  } else {
            return $this->render('updatead', [
                    'model' => $model,
                ]);
          } 
    }
	
	public function actionUpdate1copy($id)
    {
        $model = $this->findModel($id);
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        $cash_id=$model->bank_id;
        
        if($model->load(Yii::$app->request->post()))
        {
        $model->cheque_date=date("Y-m-d", strtotime($model->cheque_date));
        if($model->vtype==1){$model->bank_id=$_POST['ref'];}
        
        if(!empty($model->remarks)){$model->remarks=str_replace("'","",$model->remarks);}
        if ($model->save()) 
        {
			//echo 123;exit;
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			// $sqlt2 = "SELECT * from cconfig where type=15";
			// $resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$_POST['ref'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				
				$tt=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM tax where tax_id='".$_POST['acc1'.$j]."'";
	         $result = $conn->createCommand($sql)->queryOne();
				  $ta = $ta+$_POST['tax1'.$j];
					
					if(isset($_POST['tu'.$j]) && $_POST['tu'.$j]>0)
				 			{
				 			    
					$sql = "Update payment SET date='".$model->create_date."',salecenter='".$model->salecenter."',referanceid='". $result['aid']."',amount1='".$_POST['tax1'.$j]."' Where id='".$_POST['tu'.$j]."'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 			}
				 			else
				 			{
				 			    
					$sql = "INSERT into payment SET salecenter='".$model->salecenter."',referanceid='". $result['aid']."',vid=".$insert_id.", pfor='1',amount1='".$_POST['tax1'.$j]."',date='".$model->create_date."',status='1',type='1',remarks='Tax Paid',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 			}
					$tt=$tt+$_POST['tax1'.$j];	}
				$j=$j+1;
				}while($j < ($_POST['totalexp1']+1));
				}
				
				$sql = "Update payment SET date='".$model->create_date."',remarks='".$model->remarks."',salecenter='".$model->salecenter."',referanceid='". $model->pt_id."',amount='".$model->amount."' Where vid='".$model->id."' AND pfor=1 AND amount>0 AND type=0";
			    \Yii::$app->db->createCommand($sql)->execute();
				
				$sql = "Update payment SET date='".$model->create_date."',remarks='".$model->remarks."',salecenter='".$model->salecenter."',referanceid='". $ref."',amount1='".($model->amount-$tt)."' Where vid='".$model->id."' AND pfor=1 AND amount1>0 AND type=0";
			    \Yii::$app->db->createCommand($sql)->execute();
				
				
			        $a='Pay Bill';
                    $at='Update';
                    $u=$insert_id;
                    $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index1']);
        } 
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update1', [
                    'model' => $model,
                ]);
            }
        }
    }
    public function actionUpdate2($id)
    {
        $model = $this->findModel($id);
        if($model->load(Yii::$app->request->post()))
        {
        if(isset($_POST['states-select']) && !empty($_POST['states-select']))
        {
            $model->pt_id=$_POST['states-select'];
        }
        if(isset($model->slipdate) && !empty($model->slipdate))
        {
            $model->slipdate=date("Y-m-d", strtotime($model->slipdate));
        }
        if ($model->save()) 
        {
            $connection = Yii::$app->getDb();		
			
			$sql = "Update payment SET salecenter='".$model->salecenter."',referanceid='".$model->pt_id."',amount='".$model->amount."',amount1='0' where vid=".$model->id." AND pfor='1' AND amount>0";
			\Yii::$app->db->createCommand($sql)->execute();
			
			$sql = "Update payment SET salecenter='".$model->salecenter."',referanceid='".$model->bank_id."',amount1='".$model->amount."',amount='0' where vid=".$model->id." AND pfor='1' AND amount1>0";
			\Yii::$app->db->createCommand($sql)->execute();
			
            
            $insert_id = $model->id;
            $a='Pay Bill';
            $at='Update';
            $u=$insert_id;
            $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
			return $this->redirect(['index2']);
        }else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update2', [
                    'model' => $model,
                ]);
            }
        }
        }else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('_update', [
                    'model' => $model,
                ]);
            }
            else
            {
                return $this->render('update2', [
                    'model' => $model,
                ]);
            }
        }
    }
    
	public function actionJv()
    {
		$model = new Transaction();
		if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        
        $connection = Yii::$app->getDb();$sql_max = "SELECT MAX(vno) from transaction";
        $result_max = $connection->createCommand($sql_max)->queryOne();
        
        $model->vno=($result_max['MAX(vno)']+1);
        
        /*
		    $filename='';
            if(isset($_FILES['image']) && !empty($_FILES['image']['tmp_name']))
            {
                $rnd= rand(0, 9999);
                $pic=$_FILES['image']['name'];
                $filename=$rnd.$pic;
                move_uploaded_file($_FILES["image"]["tmp_name"],'img/transaction/'.$filename);
                $model->fc=$filename;
            }  
        */    
          
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{

            $sql_max = "SELECT MAX(vno) from transaction WHERE salecenter='".$model->salecenter."' 
            and vtype='".$model->vtype."'";
            $result_max = $connection->createCommand($sql_max)->queryOne();

		    $model = $this->findModel($model->id);
		    $model->vno=($result_max['MAX(vno)']+1);
		    $model->save();
		    
            $insert_id = Yii::$app->db->getLastInsertID();
			$model->create_date;
			$i=0;
			$conn=Yii::$app->getDb();  
			$sql= "SELECT MAX(jvid) FROM payment";
			$result = $conn->createCommand($sql)->queryOne();
		   
			$vno=0;
			if(isset($_POST['totalexp']) && $_POST['totalexp']>0)
			{
				$to=0;$to1=0;
				do
				{
				    $i=$i+1;$type='Other';$am=0;
					if(($_POST['ac'.$i]!=='' && $_POST['amountd'.$i]!=='') )
					{
						$to=$to+$_POST['amountd'.$i];
						$to1=$to1+$_POST['amountc'.$i];
					}			
				}while($_POST['totalexp'] > $i);
			
				if($to!==$to1 or ($to+$to1)==0)
				{
					echo 'Total Debit and Total Credit amount must be equal and greater then 0'; exit;
				}
			
				$i=0;
				do
				{	
					$i=$i+1;$type='Other';$am=0;
					if(($_POST['ac'.$i]!=='' && $_POST['amountd'.$i]!=='') )
					{
                        $remarks='';
                        if(!empty($_POST['remarks'.$i]))
                        {
                            $remarks=str_replace("'","\'",$_POST['remarks'.$i]);
                        }
                        
            //             $sqlac= "SELECT * from accounts WHERE id='".$_POST['ac'.$i]."' AND type=1";
			         //   $resultac = $conn->createCommand($sqlac)->queryOne();
			         //   
			            
			            
			            
			         //   if($resultac['id']>0 && $_POST['amountc'.$i]>0)
			         //   {
			         //       $credit=$_POST['amountc'.$i];
			         //       if($credit>0)
			         //       { 
    			     //           $sqldue= "SELECT * from payment WHERE referanceid='".$resultac['id']."' AND pfor=2 AND gl=0 ORDER BY id ASC";
    			     //           $resultdue = $conn->createCommand($sqldue)->queryAll();
    			     //           foreach($resultdue as $row)
    			     //           {
    			     //               $sqlpaid= "SELECT SUM(amount1) as credit from payment WHERE sid='".$row['id']."' AND pfor=1";
    			     //               $resultpaid = $conn->createCommand($sqlpaid)->queryOne(); 
    			                    
    			     //               $balance=$row['amount']-$resultpaid['credit'];
    			                    
    			     //               if($balance>0)
    			     //               {
    			     //                   $sid=$row['id'];
    			                        
    			     //                       $enter=0;
    			     //                       if($credit>$balance){$enter=$balance;} 
    			     //                       if($balance>$credit){$enter=$credit;}
    			                            
    			     //                       $sql = "INSERT INTO `payment`(`cost_center`,`branch_id`,`salecenter`,`referanceid`, 
            //                                 `pfor`, `vid`, `amount`, `amount1`, `date`, `remarks`, `type`, `status`,`jvid`, `createdate`,`gl`,`sid`) 
            //         						value('".$_POST['cost_center'.$i]."','".$model->branch_id."','".$model->salecenter."',
            //                                 '".$_POST['ac'.$i]."','6','".$model->id."','0',
            //                                 '".$enter."','".$model->create_date."','".$remarks."','1','1','".$vno."',
            //                                 '".date('Y-m-d')."',0,'".$sid."')";
            //         						\Yii::$app->db->createCommand($sql)->execute();
                    						
            //         						$credit=$credit-$enter;
            //         						$balance=$balance-$enter;
                    				   
    			     //               }
    			                    
    			     //           } 
			         //       }
			                
			         //   }
			            
			         //   else
			         //   {
                            $sid=0;
                            $vno=0;
                            if(isset($_POST['ins'.$i]) && $_POST['ins'.$i]>0)
                            {
                                $sid=$_POST['ins'.$i];
                                
                               $sqls= "SELECT id from payment WHERE id='".$sid."' AND pfor=2 AND remarks='Receivable Amount'";
    			                $results = $conn->createCommand($sqls)->queryOne(); 
                                if($results['id']>0)
                                {
                                    $vno=1;
                                }
                            }
    						$sql = "INSERT INTO `payment`(`cost_center`,`branch_id`,`salecenter`,`referanceid`, 
                            `pfor`, `vid`, `amount`, `amount1`, `date`, `remarks`, `type`, `status`,`jvid`, `createdate`,`gl`,`sid`) 
    						value('".$_POST['cost_center'.$i]."','".$model->branch_id."','".$model->salecenter."',
                            '".$_POST['ac'.$i]."','6','".$model->id."','".$_POST['amountd'.$i]."',
                            '".$_POST['amountc'.$i]."','".$model->create_date."','".$remarks."','1','1','".$vno."',
                            '".date('Y-m-d')."',0,'".$sid."')";
    						\Yii::$app->db->createCommand($sql)->execute();
						
			         //   }
					}	
				}while($_POST['totalexp'] > $i);
				
                $a='13'; // JV ID in system_activities
                $at='Create';
                $u=$model->id;
                $memberplot_id = "VoucherNo: ".$model->vno;
                $details = "VoucherDate: ".$model->create_date.",ReceiptType: Journal Voucher, Receipt#: ".$model->receipt.", Amount: ".$model->amount.", Narration: ".$model->remarks." ";
                Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$memberplot_id);
                
                
			    echo 1;
				//return $this->redirect(['jvlist']);
			}
			else
			{
			    echo 2;
				//return $this->redirect(['jvlist']);
			}
		}
		 else
            {
				
                return $this->render('jv', [
                    'model' => $model,
                ]);
            }
	
	}
    
	public function actionJvu($id)
    {
		$model = $this->findModel($id);
		if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $model->create_date=date("Y-m-d", strtotime($_POST['date']));
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
		  //  print_r($model);exit;
            $insert_id = $model->id;
            //echo $model->create_date;exit;
			$model->create_date;
			$i=0;
			$conn=Yii::$app->getDb();  
			$sql= "SELECT MAX(jvid) FROM payment";
			$result = $conn->createCommand($sql)->queryOne();
		   
			$vno=0;
			if(isset($_POST['totalexp']) && $_POST['totalexp']>0)
			{
				$to=0;$to1=0;
				do
				{	
				    $i=$i+1;$type='Other';$am=0;
					if(($_POST['ac'.$i]!=='' && $_POST['amountd'.$i]!=='') )
					{
						$to=$to+$_POST['amountd'.$i];
						$to1=$to1+$_POST['amountc'.$i];
					}			
				}while($_POST['totalexp'] > $i);
			
				if($to!==$to1 or ($to+$to1)==0)
				{
					echo 'Total Debit and Total Credit amount must be equal and greater then 0'; exit;
				}
			
				$i=0;
				do
				{
                    $remarks='';
                    
					$i=$i+1;$type='Other';$am=0; 
                    if(!empty($_POST['remarks'.$i]))
                    {
                        $remarks=str_replace("'","\'",$_POST['remarks'.$i]);
                    }
                    $vno=0;
                    $sid=0;
                    
                    if(isset($_POST['ins'.$i]) && $_POST['ins'.$i]>0){
                        $sid=$_POST['ins'.$i];
                        $sqls= "SELECT id from payment WHERE id='".$sid."' AND pfor=2 AND remarks='Receivable Amount'";
    		            $results = $conn->createCommand($sqls)->queryOne(); 
                        if($results['id']>0)
                        {
                            $vno=1;
                        }
                    }
					if(($_POST['ac'.$i]!=='' && $_POST['amountd'.$i]!=='' && isset($_POST['pid'.$i]) && $_POST['pid'.$i]>0) )
					{
						$sql = "Update `payment` SET cost_center='".$_POST['cost_center'.$i]."',
                        `branch_id`='".$model->branch_id."',`salecenter`='".$model->salecenter."',
                        `date`='".$model->create_date."',`referanceid`='".$_POST['ac'.$i]."',
                        `amount`='".$_POST['amountd'.$i]."', `amount1`='".$_POST['amountc'.$i]."',
                         `remarks`='".$remarks."',jvid='".$vno."',sid='".$sid."' Where id='".$_POST['pid'.$i]."'";
						\Yii::$app->db->createCommand($sql)->execute();
					}
					else if($_POST['ac'.$i]!=='' && $_POST['amountd'.$i]!=='')
					{
					    $sql = "INSERT INTO `payment`(`cost_center`,`branch_id`,`salecenter`,`referanceid`, `pfor`, 
                        `vid`, `amount`, `amount1`, `date`, `remarks`, `type`, `status`,`jvid`, `createdate`,`gl`,`sid`) 
					    value('".$_POST['cost_center'.$i]."','".$model->branch_id."','".$model->salecenter."',
                        '".$_POST['ac'.$i]."','6','".$model->id."','".$_POST['amountd'.$i]."','".$_POST['amountc'.$i]."',
                        '".$model->create_date."','".$remarks."','1','1','".$vno."','".date('Y-m-d')."',0,'".$sid."')"; 
						\Yii::$app->db->createCommand($sql)->execute();
					}
				}while($_POST['totalexp'] > $i);

    //             $a='JV';
    //             $at='Update';
    //             $u=$model->id;
    //             $d = '';
    //             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
			 //   
			    
                $a='13'; // JV ID in system_activities
                $at='Update';
                $u=$model->id;
                $memberplot_id = "VoucherNo: ".$model->vno;
                $details = "VoucherDate: ".$model->create_date.",ReceiptType: Journal Voucher, Receipt#: ".$model->receipt.", Amount: ".$model->amount.", Narration: ".$model->remarks." ";
                Yii::$app->mycomponent->NewActivityLog($a,$at,$u,$details,$memberplot_id);
                echo 1;
				//return $this->redirect(['jvlist']);
			}
			else
			{
			    echo 2;
				//return $this->redirect(['jvlist']);
			}
		}
		 else
            {
				
                return $this->render('jvu', [
                    'model' => $model,
                ]);
            }
	}
	
    public function actionImportpayment()
    {
		$insert_id=0;
        $model = new Transaction();
       if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$insert_id = $model->id;
			$conn=Yii::$app->getDb(); 
			$sqlt2 = "SELECT * from cconfig where type=1";
			$resultt2 = $conn->createCommand($sqlt2)->queryOne();
			if($model->vtype==1 || $model->vtype==3){$ref=$resultt2['aid'];}
			if($model->vtype==2 || $model->vtype==4){$ref=$model->bank_id;}
				$ta=0;
				if(isset($_POST['totalexp1']) && $_POST['totalexp1'] > 0){
				$j=1;
				do{
				if(isset($_POST['acc1'.$j])){
					$connection = Yii::$app->getDb();
					$status=1;
					$sql= "SELECT * FROM taxdefination where id='".$_POST['acc1'.$j]."'";
	                $result = $conn->createCommand($sql)->queryOne();
					$tac=0;$tad=0;
					if($model->vtype==1 or $model->vtype==2){$tac=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					if($model->vtype==3 or $model->vtype==4){$tad=$_POST['tax1'.$j];$ta=$ta+$_POST['tax1'.$j];}
					
					$sql = "INSERT into payment SET referanceid='".$result['aid']."',vid=".$insert_id.", pfor='1',amount='".$tad."',amount1='".$tac."',date='".date('Y-m-d')."',status='1',type='1',remarks='OK',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
						}
				$j=$j+1;
	            }while($j < ($_POST['totalexp1']+1));
	        }	
				
				if($model->vtype==1 || $model->vtype==2)
				{
					$sql = "INSERT into payment SET referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount1='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
			
					$sql = "INSERT into payment SET referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
				}
				
				if($model->vtype==3 || $model->vtype==4)
				{
					$sql = "INSERT into payment SET referanceid='".$ref."',vid=".$insert_id.", pfor='1',amount='".($model->amount-$ta)."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
			
					$sql = "INSERT into payment SET referanceid='".$model->pt_id."',vid=".$insert_id.", pfor='1',amount1='".$model->amount."',date='".$model->create_date."',status='1',remarks='".$model->remarks."',createdate='".date('Y-m-d')."',type='0',jvid='0'";
			        \Yii::$app->db->createCommand($sql)->execute();
				}
				
				if(isset($_POST['totalexp']) && $_POST['totalexp'] > 0){
				$i=1;
				do{
				 if(isset($_POST['acc'.$i]) and isset($_POST['acc'.$i])=='on')
				 {
					$connection = Yii::$app->getDb();
					$status=1;
					
					$sql = "INSERT into payment SET referanceid='".$_POST['acc'.$i]."',vid=".$insert_id.", pfor='1',amount='".($_POST['dam'.$i])."',date='".date('Y-m-d')."',status='1',remarks='".$_POST['details'.$i]."',createdate='".date('Y-m-d')."'";
			        \Yii::$app->db->createCommand($sql)->execute();
				 }
				$i=$i+1;
				}while($i < ($_POST['totalexp']+1));
				}
				
			$a='Import Payment';
                    $at='Create';
                    $u=$insert_id;
                   $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
				
			return $this->redirect(['index']);
        } else {

            $request = Yii::$app->request;

            if($request->isAjax)
            {
                return $this->renderPartial('importpayment', [
                    'model' => $model,
                ]);
            }
            else
            {
				
                return $this->render('importpayment', [
                    'model' => $model,
                ]);
            }
        }
    }
	public function actionVerified($id)
    {
        $model = $this->findModel($id);
		return $this->render('_form2', ['model' => $model,]);
    }
	public function actionVerify($id)
    {
      $connection = Yii::$app->getDb();
	  $sql1 = "Update transaction SET ver='1' where id='".$_REQUEST['id']."'";
	  \Yii::$app->db->createCommand($sql1)->execute();
	  return $this->redirect(Yii::$app->urlManager->baseUrl.'/index.php?r=payment/forverification');
	}
    
	public function actionJvlist()
    {
		

        $request = Yii::$app->request;
        
        $searchModel = new TransactionSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->orderBy(['slipdate' => SORT_DESC,]);
        if($_SESSION["user_array"]["usertype"]==2)
		{
		$dataProvider->query->andFilterWhere(['transaction.user_id'=>$_SESSION["user_array"]["id"]]);
		}
		$dataProvider->query->andFilterWhere(['transaction.vtype'=>7]);
        $dataProvider->pagination->pageSize=$request->get("pagesize",20);
         if(!isset($_REQUEST['page'])){$_REQUEST['page']=1;}
		$dataProvider->pagination->page=($_REQUEST['page']-1);
		

        if($request->isAjax)
        {
            return $this->renderPartial('_index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else
        {
            return $this->render('jvlist', [
				'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    
    }
	
	public function actionPettylist()
    {

        $request = Yii::$app->request;
        
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->Where('jv=1 and jv_ref=0 ');
        $dataProvider->pagination->pageSize=$request->get("pagesize",20);
        $dataProvider->pagination->page=$request->get("pageno",0);

            return $this->render('pettylist', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
    }
    
    public function actionJvprint()
    {
        $login_id = $_SESSION['user_array']['id'] ?? null;
        $id = $_REQUEST['id'] ?? null;
    
        if (!$id) {
            throw new NotFoundHttpException("No transaction ID provided.");
        }
    
        if (!$login_id) {
            throw new NotFoundHttpException("Login session does not exist.");
        }
    
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("The requested transaction does not exist.");
        }
    
        // Logging activity
        $activity_type = '13'; // Journal Voucher
        $action_type = 'Print';
        $transaction_id = $model->id;
    
        $memberplot_id = "VoucherNo: " . $model->vno;
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: Journal Voucher"
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
    
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot_id);
    
        // Redirect after logging
        $url = Yii::$app->urlManager->baseUrl . "/Print/jv1.php?id=$id&mem=$login_id";
        return $this->redirect($url);
    }
    
    
    public function actionAdjprint1()
    {
        $login_id = $_SESSION['user_array']['id'] ?? null;
        $id = $_REQUEST['id'] ?? null;
    
        if (!$id) {
            throw new NotFoundHttpException("No transaction ID provided.");
        }
    
        if (!$login_id) {
            throw new NotFoundHttpException("Login session does not exist.");
        }
    
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("The requested transaction does not exist.");
        }
    
        // Logging activity
        $activity_type = '14'; // Journal Voucher
        $action_type = 'Print';
        $transaction_id = $model->id;
    
        
        $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
        
        if(!$memberplot)
        {
            $memberplot['ref'] = "VoucherNo: ". $model->vno;
        }
        
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: Journal Voucher"
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
    
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
        $url = Yii::$app->urlManager->baseUrl . "/Print/transaction_rec1.php?id=$id&mem=$login_id";
        return $this->redirect($url);
    }
    
    
    public function actionAdjprint2()
    {
        $login_id = $_SESSION['user_array']['id'] ?? null;
        $id = $_REQUEST['id'] ?? null;
    
        if (!$id) {
            throw new NotFoundHttpException("No transaction ID provided.");
        }
    
        if (!$login_id) {
            throw new NotFoundHttpException("Login session does not exist.");
        }
    
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("The requested transaction does not exist.");
        }
    
        // Logging activity
        $activity_type = '14'; // Journal Voucher
        $action_type = 'Print';
        $transaction_id = $model->id;
        
        $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
        
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: Journal Voucher"
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
    
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
    
        // Redirect after logging
        $url = Yii::$app->urlManager->baseUrl . "/Print/transaction.php?id=$id&mem=$login_id";
        return $this->redirect($url);
    }

    
    public function actionDeletejv(){ // New By Qamar
        
        $id = $_REQUEST['id']; // Consider using Yii::$app->request->get('id') for better security
        $connection = Yii::$app->getDb();
    
        // Fetch the model before deletion for logging
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("The requested transaction does not exist.");
        }
    
        // Move related data to recycle tables
        $sql = "INSERT INTO payment_recyle SELECT * FROM payment WHERE pfor = 1 AND vid = :id";
        Yii::$app->db->createCommand($sql)->bindValue(':id', $id)->execute();
    
        $sql1 = "INSERT INTO transaction_recyle SELECT * FROM transaction WHERE id = :id";
        Yii::$app->db->createCommand($sql1)->bindValue(':id', $id)->execute();
    
        // Delete from main tables
        $model->delete();
    
        $connection->createCommand()
            ->delete('payment', ['vid' => $id, 'pfor' => 6])
            ->execute();
    
        // Activity Log
        $activity_type = '13'; // ID for Journal Voucher in system_activities
        $action_type = 'Delete';
        $transaction_id = $model->id;
    
        $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
        
        if(!$memberplot)
        {
            $memberplot['ref'] = "VoucherNo: ". $model->vno;
        }
        
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: Journal Voucher"
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
            
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
    
        return $this->redirect(Yii::$app->urlManager->baseUrl . '/index.php?r=transaction/jvlist');
    }

// 	public function actionDeletejv(){ //OLD
// 		$connection = Yii::$app->getDb();
		
// 		$sql = "INSERT into payment_recyle Select * from payment where pfor=1 AND vid='".$_REQUEST['id']."'";
// 	    \Yii::$app->db->createCommand($sql)->execute();
	    
// 	    $sql1 = "INSERT into transaction_recyle Select * from transaction where id='".$_REQUEST['id']."'";
// 	    \Yii::$app->db->createCommand($sql1)->execute();
		
// 		$this->findModel($_REQUEST['id'])->delete();
// 			$connection ->createCommand()
//             ->delete('payment', 'vid='.$_REQUEST['id'].' AND pfor=6')
//           	->execute();

//               $a='JV';
//                     $at='Delete';
//                     $u=$_REQUEST['id'];//Yii::$app->db->getLastInsertID();
//                     $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 	        return $this->redirect(Yii::$app->urlManager->baseUrl.'/index.php?r=transaction/jvlist');
// 	}
	
	
    public function actionDeletesp(){
		$connection = Yii::$app->getDb();
		
		
		
			$connection ->createCommand()
            ->delete('payment', 'id='.$_REQUEST['pid'].'')
           	->execute();
            $a='Receive Payment';
                    $at='Deletes';
                    $u=$_REQUEST['pid'];//Yii::$app->db->getLastInsertID();
                    $d = '';
            Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);

	        return $this->redirect(Yii::$app->urlManager->baseUrl.'/index.php?r=transaction/update&id='.$_REQUEST['vid']);
	}
	public function actionDeleteexp(){
		$connection = Yii::$app->getDb();		
			$connection ->createCommand()
            ->delete('payment', 'id='.$_REQUEST['pid'].'')
           	->execute();
	        return $this->redirect(Yii::$app->urlManager->baseUrl.'/index.php?r=transaction/update1&id='.$_REQUEST['vid']);
	}
	public function actionDeletetax(){
		$connection = Yii::$app->getDb();		
			$connection ->createCommand()
            ->delete('payment', 'id='.$_REQUEST['pid'].'')
           	->execute();
	        return $this->redirect(Yii::$app->urlManager->baseUrl.'/index.php?r=transaction/update&id='.$_REQUEST['vid']);
	}
	public function actionDeletepp(){
			$connection = Yii::$app->getDb();		
			$connection ->createCommand()
            ->delete('payment', 'id='.$_REQUEST['pid'].'')
           	->execute();
	        return $this->redirect(Yii::$app->urlManager->baseUrl.'/index.php?r=transaction/update&id='.$_REQUEST['vid']);
	}
	

    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
     
    
    
    public function actionDelete($id)
    {
        $connection = Yii::$app->getDb();
    
        // Fetch transaction model before deletion
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("Transaction not found.");
        }
    
        $sql = "INSERT INTO payment_recyle SELECT * FROM payment WHERE pfor = 1 AND vid = :id";
        Yii::$app->db->createCommand($sql)->bindValue(':id', $id)->execute();
    
        $sql1 = "INSERT INTO transaction_recyle SELECT * FROM transaction WHERE id = :id";
        Yii::$app->db->createCommand($sql1)->bindValue(':id', $id)->execute();
    
        $connection->createCommand()
            ->delete('payment', ['pfor' => 1, 'vid' => $id])
            ->execute();
    
        $model->delete();
    
        // Logging
        $status = $_REQUEST['status'] ?? null;
        if ($status == 1) { // For Recievings
            $activity_type = '16'; // Receive Installments id from System Activities
        }
        else
        { // For Installments Recieveings
            $activity_type = '15'; // Receive Installments id from System Activities
        }
        $action_type = 'Delete';
        $transaction_id = $model->id;
        $voucher_type = "N/A";
        if($model->vtype==3){
            $voucher_type = 'Cash Reciept';
        }
        else if($model->vtype==4)
        {
            $voucher_type = 'Bank Reciept';
        }
    
        $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
        
        if(!$memberplot)
        {
            $memberplot['ref'] = "VoucherNo: " .$model->vno;  
        }
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: " . $voucher_type
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
    
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
        
        // Redirect
        if ($status == 1) {
            return $this->redirect(['index']);
        } else {
            return $this->redirect(['ins_recieved']);
        }
    }

//     public function actionDelete($id)
//     {
//         $connection = Yii::$app->getDb();		
	  
// 		$sql = "INSERT into payment_recyle Select * from payment where pfor=1 AND vid='".$id."'";
// 	    \Yii::$app->db->createCommand($sql)->execute();
	    
// 	    $sql1 = "INSERT into transaction_recyle Select * from transaction where id='".$id."'";
// 	    \Yii::$app->db->createCommand($sql1)->execute();
		
		
		
// 		$connection ->createCommand()->delete('payment', ['AND', ['pfor' => '1'], ['vid' => $id]])->execute();
        
//         $this->findModel($id)->delete();
// 		if(isset($_REQUEST['status']) && $_REQUEST['status']==1)
// 		{
// 		    $a='Receive Payment';
//                 $at='Delete';
//                 $u=$id;
//                 $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
		    
// 			return $this->redirect(['index']);
// 		}
// 		else
// 		{ $a='Recieve Installments';
//                 $at='Delete';
//                 $u=$id;
//               $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 			return $this->redirect(['ins_recieved']);
// 		}
//     }
	
// 	public function actionDelete1($id)
//     {
//         $connection = Yii::$app->getDb();
        
//         $sql = "INSERT into payment_recyle Select * from payment where pfor=1 AND vid='".$id."'";
// 	    \Yii::$app->db->createCommand($sql)->execute();
	    
// 	    $sql1 = "INSERT into transaction_recyle Select * from transaction where id='".$id."'";
// 	    \Yii::$app->db->createCommand($sql1)->execute();
        
        
//         $sqlt2 = "SELECT * from transaction where id='".$id."'";
// 		$resultt2 = $connection->createCommand($sqlt2)->queryOne();
// 		if($resultt2['status_type']==10)
// 		{
// 		    $a='Refund';
//                 $at='Delete';
//                 $u=$id;
//                 $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 		}
		
// 		if($resultt2['status_type']==5)
// 		{
// 		    $a='Token Refund';
//                 $at='Delete';
//                 $u=$id;
//                 $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 		}
		
// 		if($resultt2['status_type']==3)
// 		{
// 		    $a='Pay Expense';
//                 $at='Delete';
//                 $u=$id;
//               $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 		}
		
// 		if($resultt2['status_type']==1)
// 		{
// 		    $a='Pay Bill';
//                 $at='Delete';
//                 $u=$id;
//                 $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 		}
        
// 		$connection ->createCommand()->delete('payment', ['AND', ['pfor' => '1'], ['vid' => $id]])->execute();
        
//         $this->findModel($id)->delete();

//         return $this->redirect(['index1']);
//     }
    
    public function actionDelete1($id)
    {
        $connection = Yii::$app->getDb();
    
        // Fetch transaction model before deletion
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("Transaction not found.");
        }
    
        // Move related records to recycle tables
        $sql = "INSERT INTO payment_recyle SELECT * FROM payment WHERE pfor = 1 AND vid = :id";
        Yii::$app->db->createCommand($sql)->bindValue(':id', $id)->execute();
    
        $sql1 = "INSERT INTO transaction_recyle SELECT * FROM transaction WHERE id = :id";
        Yii::$app->db->createCommand($sql1)->bindValue(':id', $id)->execute();
    
        // Determine activity based on status_type
        $statusType = $model->status_type;
        $activity = '';
        switch ($statusType) {
            case 10:
                $activity = 'Refund';
                break;
            case 5:
                $activity = 'Token Refund';
                break;
            case 3:
                $activity = 'Pay Expense';
                break;
            case 1:
                $activity = 'Pay Bill';
                break;
            default:
                $activity = 'Unknown Transaction';
        }
    
        // Log using old method (optional, legacy support)
        Yii::$app->mycomponent->Activitylog($activity, 'Delete', $id, '');
    
        // New detailed logging
        
        $activity_type = '17'; // Or use a different ID if applicable
        
        $action_type = 'Delete';
        $transaction_id = $model->id;
        // $memberplot_id = "VoucherNo: " . $model->vno;
        
        $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
        if(!$memberplot)
        {
            $memberplot['ref'] = "VoucherNo: ". $model->vno;
        }
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: $activity"
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
    
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
    
        // Delete related payments
        $connection->createCommand()
            ->delete('payment', ['pfor' => 1, 'vid' => $id])
            ->execute();
    
        // Delete the transaction itself
        $model->delete();
    
        // Redirect
        return $this->redirect(['index1']);
    }

	
	public function actionDelete2($id)
    {
        $connection = Yii::$app->getDb();
        
        $sql = "INSERT into payment_recyle Select * from payment where pfor=1 AND vid='".$id."'";
	    \Yii::$app->db->createCommand($sql)->execute();
	    
	    $sql1 = "INSERT into transaction_recyle Select * from transaction where id='".$id."'";
	    \Yii::$app->db->createCommand($sql1)->execute();
        
		$connection ->createCommand()->delete('payment', ['AND', ['pfor' => '1'], ['vid' => $id]])->execute();
        
        $this->findModel($id)->delete();

        return $this->redirect(['index2']);
    }
    
    public function actionDelete3($id)
    {
        $connection = Yii::$app->getDb();
    
        // Fetch the transaction model before deletion
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException("Transaction not found.");
        }
    
        // Move related records to recycle tables
        $sql = "INSERT INTO payment_recyle SELECT * FROM payment WHERE pfor = 1 AND vid = :id";
        Yii::$app->db->createCommand($sql)->bindValue(':id', $id)->execute();
    
        $sql1 = "INSERT INTO transaction_recyle SELECT * FROM transaction WHERE id = :id";
        Yii::$app->db->createCommand($sql1)->bindValue(':id', $id)->execute();
    
        // Optional: log different activities based on status_type
        // if ($model->status_type == 6) {
        //     Yii::$app->mycomponent->Activitylog('Adjustment', 'Delete', $id, '');
        // }
    
        // if ($model->status_type == 7) {
        //     Yii::$app->mycomponent->Activitylog('Adjust Payment', 'Delete', $id, '');
        // }
    
        // You can also log using NewActivityLog with more detail:
        $activity_type = '14'; // Adjust Payment id from System Activities
        $action_type = 'Delete';
        $transaction_id = $model->id;
        $memberplot = \Yii::$app->db->createCommand("SELECT ref FROM accounts WHERE id = '".$model->pt_id."' AND type=1 ")->queryOne();
        $details = "VoucherDate: " . $model->create_date
            . ", ReceiptType: " . $this->getReceiptType($model->status_type)
            . ", Receipt#: " . $model->receipt
            . ", Amount: " . $model->amount
            . ", Narration: " . $model->remarks;
    
        Yii::$app->mycomponent->NewActivityLog($activity_type, $action_type, $transaction_id, $details, $memberplot['ref']);
        
    
    
        // Delete related records
        $connection->createCommand()
            ->delete('payment', ['pfor' => 1, 'vid' => $id])
            ->execute();
    
        // Delete transaction
        $model->delete();
    
        return $this->redirect(['indexad']);
    }
    private function getReceiptType($status_type)
    {
        switch ($status_type) {
            case 6:
                return "Adjustment";
            case 7:
                return "Adjust Payment";
            default:
                return "Unknown";
        }
    }

// 	public function actionDelete3($id)
//     {
//         $connection = Yii::$app->getDb();
        
//         $sql = "INSERT into payment_recyle Select * from payment where pfor=1 AND vid='".$id."'";
// 	    \Yii::$app->db->createCommand($sql)->execute();
	    
// 	    $sql1 = "INSERT into transaction_recyle Select * from transaction where id='".$id."'";
// 	    \Yii::$app->db->createCommand($sql1)->execute();
        
//         $sqlt2 = "SELECT * from transaction where id='".$id."'";
// 		$resultt2 = $connection->createCommand($sqlt2)->queryOne();
// 		if($resultt2['status_type']==6)
// 		{
// 		    $a='Adjustment';
//                 $at='Delete';
//                 $u=$id;
//                 $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 		}	
// 		if($resultt2['status_type']==7)
// 		{
// 		    $a='Adjust Payment';
//                 $at='Delete';
//                 $u=$id;
//                 $d = '';
//             Yii::$app->mycomponent->Activitylog($a,$at,$u,$d);
// 		}
		
// 		$connection ->createCommand()->delete('payment', ['AND', ['pfor' => '1'], ['vid' => $id]])->execute();
        
//         $this->findModel($id)->delete();

//         return $this->redirect(['indexad']);
//     }
	
	
	public function actionDelete4($id)
    {
        $connection = Yii::$app->getDb();	
        $sql = "INSERT into payment_recyle Select * from payment where pfor=1 AND vid='".$id."'";
	    \Yii::$app->db->createCommand($sql)->execute();
	    
	    $sql1 = "INSERT into transaction_recyle Select * from transaction where id='".$id."'";
	    \Yii::$app->db->createCommand($sql1)->execute();
        
		$connection ->createCommand()->delete('payment', ['AND', ['pfor' => '1'], ['vid' => $id]])->execute();
        
        $this->findModel($id)->delete();

        return $this->redirect(['indexmr']);
    }

    /**
     * Displays sidebar Transaction model.
     * @return mixed
     */
    public function actionSidebarsummary()
    {
        return $this->renderPartial('_summarysearch');
    }

    /**
     * Displays sidebar Transaction model.
     * @return mixed
     */
     
    	public function actionMsg()
	{
		if(isset($_POST['sub']))
		{
		    if(isset($_POST['print']) && $_POST['print']=='Print')
		    {
		        $this->render('msg1');
		        exit;
		    }else{
		    $where='';
		    if (!empty($_POST['project'])){
				$where.="and project_id =".$_POST['project']."";
			}
			if (!empty($_POST['sector'])){
				$where.="and sector_id =".$_POST['sector']."";
			}
			if (!empty($_POST['plot_id'])){
				$where.="and plots.id =".$_POST['plot_id']."";
			}
		    $connection = Yii::$app->db;		
	        $sql  = "SELECT * from plots
	        Left Join streets ON (streets.id=plots.street_id)
	        where com_res='".$_POST['type']."' ".$where."";
	        $result = $connection->createCommand($sql)->queryAll();
	        foreach($result as $row)
	        {
	         $to = "somebody@example.com";
             $subject = "My subject";
             $txt = $_POST['msg'];
             $headers = "From: webmaster@example.com" . "\r\n" .
             "CC: somebodyelse@example.com";

              mail($to,$subject,$txt,$headers);   
	        }
		    echo "Message Sent Successfully";
		    }
		}
		return $this->render('msg');
//	return	$this->render('msg');
		
	}
 
    public function actionSidebarinput()
    {
        return $this->renderPartial('_sidebarinput');
    }

    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
?>