<?php
  foreach ($array as $key => $value) {
      ?>
          <style type="text/css">
            h6{
                margin: 0px 0px 8px;
                font-size: 14px;
            }
            p{
              margin: 0px 0px 8px;
            }
          </style>
          <div class="row" style="border-bottom: 1px solid #eee">
            <div class="col-md-12" style="padding: 5px 15px;">
              <h6><?=$value->classement?></h6>
              <div class="row"  style="<?=strlen($value->standard) != 0 ?'' : 'display:none'?>">
                <div class="col-md-12">
                  <h6>Standard</h6>
                  <p style=" font-size: 13px;"><?=$value->standard ?></p>
                </div>
              </div>
              <div class="row"  style="<?=strlen($value->livrable) != 0 ?'' : 'display:none'?>">
                <div class="col-md-12">
                  <h6>Livrable</h6>
                  <p style=" font-size: 13px;"><?=$value->livrable ?></p>
                </div>
              </div>
              <div class="row" style="<?=strlen($value->frais_admin) != 0 ?'' : 'display:none'?>">
                <div class="col-md-12">
                  <h6>Frais de l'administration</h6>
                  <p style=" font-size: 13px;"><?=$value->frais_admin ?></p>
                </div>
              </div>
              <div class="row" style="<?=strlen($value->penalite) != 0 ?'' : 'display:none'?>">
                <div class="col-md-12">
                  <h6>Pénalités</h6>
                  <p style=" font-size: 13px;"><?=$value->penalite ?></p>
                </div>
              </div>
            </div>
          </div>
      <?php
  }
?>