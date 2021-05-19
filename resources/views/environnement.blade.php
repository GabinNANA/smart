<?php
	if (count($array2) >0) {
		?>
			<style type="text/css">
				h4{
			    	font-size: 14px;
				    font-weight: 700;
				    margin: 0;
				}
				h6{
				    font-size: 13px;
				    margin: 0px;
				}
				p{
				    font-size: 12px;
				    font-weight: 400;
				    color:black;
				}
				h5,h2{
				    font-size: 14px;
				    font-weight: 400;
				    font-style: italic;
				    margin:0px;
				}
			</style>
			<div class="row">
		      <div class="col-md-12" style="padding:0px">
		        <h4 style="border-top: 1px solid #fff; padding: 8px 12px; font-size: 14px; font-weight: 700; width: 100%; 
		                    border-bottom: 1px solid #fff; margin: 0;background:#9c9c9c;color:white;text-align: center">Exigences environnementales</h4>
		      </div>
		    </div>
		<?php
	}
	foreach ($array2 as $key => $value) {
		?>

		    <div class="row" style="border-bottom: 1px solid rgb(132 46 27 / 62%);;width: 100%;">
		      <div class="col-md-12">
		        <h6><?=$value->classement?>
		        </h6>
		        <div class="row" style="<?=strlen($value->standard !=0) ? '' : 'display: none'?>">
		          <div class="col-md-12">
		            <h4>Standard</h4>
		            <p><?=$value->standard?></p>
		          </div>
		        </div>
		        <div class="row" style="<?=strlen($value->livrable !=0) ? '' : 'display: none'?>">
		          <div class="col-md-12">
		            <h4>Livrable</h4>
		            <p><?=$value->livrable?></p>
		          </div>
		        </div>
		        <div class="row">
		          <div class="col-md-1" style="<?=strlen($value->validite !=0) ? '' : 'display: none'?>">
		            <ion-icon style="font-size: 32px;    color: #842e1b;" name="alarm-outline"></ion-icon>
		          </div>
		          <div class="col-md-11" style="padding-left: 15px;<?=strlen($value->validite !=0) ? '' : 'display: none'?>">
		            <p style="  color: #842e1b;margin: 0;font-size: 14px;display: flex;justify-content: end;align-items: center">
		              <b>Validité du livrable</b>
		            </p>
		            <p style="margin: 0;font-size: 14px;">
		              <?=$value->validite?>
		            </p>
		          </div>
		          <div class="col-md-1" style="<?=strlen($value->delai !=0) ? '' : 'display: none'?>">
		            <ion-icon style="font-size: 32px;    color: #842e1b;" name="calendar-outline"></ion-icon>
		          </div>
		          <div class="col-md-11" style="padding-left: 15px;<?=strlen($value->delai !=0) ? '' : 'display: none'?>">
		            <p style="  color: #842e1b;margin: 0;font-size: 14px;display: flex;justify-content: end;align-items: center">
		              <b>Délai de l'étude</b>
		            </p>
		            <p style="margin: 0;font-size: 14px;">
		              <?=$value->delai?>
		            </p>
		          </div>
		          <div class="col-md-1" style="<?=strlen($value->cout_etude !=0) ? '' : 'display: none'?>">
		            <ion-icon style="font-size: 32px;    color: #842e1b;" name="cash-outline"></ion-icon>
		          </div>
		          <div class="col-md-11" style="padding-left: 15px;<?=strlen($value->cout_etude !=0) ? '' : 'display: none'?>">
		            <p style="  color: #842e1b;margin: 0;font-size: 14px;display: flex;justify-content: end;align-items: center">
		              <b>Coût de l'étude</b>
		            </p>
		            <p style="margin: 0;font-size: 14px;">
		              <?=$value->cout_etude?>
		            </p>
		          </div>
		        </div>
		        <div class="row" style="<?=strlen($value->frais_admin !=0) ? '' : 'display: none'?>">
		          <div class="col-md-12">
		            <h4>Frais de l'administration</h4>
		            <p><?=$value->frais_admin?></p>
		          </div>
		        </div>
		        <div class="row" style="<?=strlen($value->penalite !=0) ? '' : 'display: none'?>">
		          <div class="col-md-12">
		            <h4>Pénalités</h4>
		            <p><?=$value->penalite?></p>
		          </div>
		        </div>
		      </div>
		    </div>
		<?php
	}
	if (count($array) > 0) {
		?>
		<style type="text/css">
			h4{
		    	font-size: 14px;
			    font-weight: 700;
			    margin: 0;
			}
			h6{
			    font-size: 13px;
			    margin: 0px;
			}
			p{
			    font-size: 12px;
			    font-weight: 400;
			    color:black;
			}
			h5,h2{
			    font-size: 14px;
			    font-weight: 400;
			    font-style: italic;
			    margin:0px;
			}
		</style>
			<div class="row">
		      <div class="col-md-12" style="padding:0px">
		        <h4 style="border-top: 1px solid #fff; padding: 8px 12px; font-size: 14px; font-weight: 700; width: 100%; 
		                  border-bottom: 1px solid #fff; margin: 0;background:#9c9c9c;color:white;text-align: center">Mesures
		          obligatoires</h4>
		      </div>
		    </div>
		<?php
	}
	foreach ($array as $key => $value) {
		?>
			
		    <div class="row" style="border-bottom: 1px solid rgb(132 46 27 / 62%);width: 100%;">
		      <div class="col-md-12" style="padding: 0px;">
		        <h4 style="margin:0px;padding:14px 15px;background: #842e1b;color:white;<?=strlen($value->titre) !=0 ? '' : 'display: none'?>">
		          <?=$value->titre?></h4>
		        <p style="margin: 7px 0px;padding:0px 15px;<?=strlen($value->standard) !=0 ? '' : 'display: none'?>">
		          <?=$value->standard?>
		        </p>
		      </div>
		    </div>
		<?php
	}
?>