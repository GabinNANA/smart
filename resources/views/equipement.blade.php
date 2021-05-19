<?php
    if (isset($arrayresult1)) {
      ?>
        <style type="text/css">
          h6,h4{
              margin: 0px 0px 8px;
              font-size: 14px;
          }
          p{
            margin: 0px 0px 8px;
          }
        </style>
        <h4 style="font-weight: 700;    margin: 5px 0px;color: #842e1b">Historique verification environnement <?=$nomequipe?></h4>
    <?php
      foreach ($arrayresult1 as $key => $value) {
          ?>
            <ion-row>
              <ion-row style="padding: 0px 10px;border-bottom: 1px solid #842e1b">
                <ion-col size="12">
                  <h4>Date de réalisation
                  </h4>
                </ion-col>
                <ion-col size="12">
                  <?=date('d-m-Y',strtotime($value->date_prevue))?>
                </ion-col>
                <ion-col size="12">
                  <h4>Société,utilisateur ou presonne compétente / organisme agrée</h4>
                </ion-col>
                <ion-col size="12">
                  <?=$value->fournisseur?>
                </ion-col>
                <ion-col size="12">
                  <h4>Prévoir le renouvellement en : <?=date('d-m-Y',strtotime($value->date_reel))?></h4>
                </ion-col>
              </ion-row>
            </ion-row>
          <?php
      }
    }
    if (isset($resultat)) {
        if (count($resultat) != 0) {
            ?>
                <h4 style="font-weight: 700;    margin: 5px 0px;color: #842e1b">Géneralités</h4>
                <p style="margin: 7px 0px;">
                    Les matériels, engins, installations et dispositifs doivent faire l'objet de contrôles périodiques 
                    
                    selon le texte reglémentaire art. 64 arrêté 039<br>
                </p>
            <?php
        }
        foreach ($resultat as $key => $value) {
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
                <div class="row"
                    style="padding: 0px; margin-bottom: 0px;">
                    <div class="col-md-12" style="padding: 0px">
                        <h4 style="margin:0px;padding:10px 8px;background: #666;color:white;font-size: 14px"><?=$key?></h4>
                    </div>
                    <?php
                        foreach ($value as $keys => $value1) {
                            ?>
                                <div class="row" style="border-bottom: 1px solid #eee">
                                    <div class="col-md-12" style="padding: 5px 12px;">
                                        <h6 style="font-weight: 700; margin: 5px 0px;color: #842e1b;<?=strlen($value1->type) == 0 ? 'display: none' : ''?>">Type d'utilisation</h6>
                                        <p style="margin: 7px 0px;<?=strlen($value1->type) == 0 ? 'display: none' : ''?>">
                                            <?=$value1->type?>
                                        </p>
                                        <h6 style="font-weight: 700; margin: 5px 0px;color: #842e1b;<?=strlen($value1->moment_frequence) == 0 ? 'display: none' : ''?>">Moment ou fréquence de vérification</h6>
                                        <p style="margin: 7px 0px;<?=strlen($value1->moment_frequence) == 0 ? 'display: none' : ''?>">
                                            <?=$value1->moment_frequence?>
                                        </p>
                                        <h6 style="font-weight: 700; margin: 5px 0px;color: #842e1b;<?=strlen($value1->personne_organisme) == 0 ? 'display: none' : ''?>">Personne ou organisme chargé de la vérification</h6>
                                        <p style="margin: 7px 0px;<?=strlen($value1->personne_organisme) == 0 ? 'display: none' : ''?>">
                                            <?=$value1->personne_organisme?>
                                        </p>
                                        <h6 style="font-weight: 700; margin: 5px 0px;color: #842e1b;<?=strlen($value1->reference) == 0 ? 'display: none' : ''?>">Texte reglémentaire</h6>
                                        <p style="margin: 7px 0px;<?=strlen($value1->reference) == 0 ? 'display: none' : ''?>">
                                            <?=$value1->reference?>
                                        </p>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                    
                </div>
            <?php
        }
    }    
    if (isset($array)) {
      ?>
        <style type="text/css">
          h6,h4{
              margin: 0px 0px 8px;
              font-size: 14px;
          }
          p{
            margin: 0px 0px 8px;
          }
        </style>
      <?php
        if ($isavant != 0) {
            ?>
                <div class="row" >
                  <div class="col-md-12">
                    <h4 style="margin:5px 0px;font-weight: 500;border: 1px solid #616161;padding: 10px;font-size: 12px;
                          background: #616161;color: white;">
                      Date derniere vérification avant la première utilisation
                    </h4>
                  </div>
                </div>
                <?php
                    foreach ($array[0] as $key => $value) {
                        ?>
                            <div class="row" style="padding: 0px 10px;">
                              <div class="col-md-12">
                              <h4>Date derniere vérification</h4>
                            </div>
                              <div class="col-md-12">
                                <?=date('d-m-Y',strtotime($value->date_derniere_avant_mise_service))?>
                              </div>
                              <div class="col-md-12">
                                <h4>Société,utilisateur ou presonne compétente / organisme agrée</h4>
                              </div>
                              <div class="col-md-12">
                                <?=$value->fait_par_avant_mise_service?>
                              </div>
                            </div>
                            <div class="row" style="padding: 0px 10px;">
                              <div class="col-md-12">
                                <h4>Date prochaine vérification probable</h4>
                              </div>
                              <div class="col-md-12">
                                <label style="color:red;font-weight: 700" id="pverificationavant"><?=date('d-m-Y',strtotime($value->date_prevue_avant_mise_service))?></label>
                              </div>
                            </div>
                        <?php
                    }
                ?>
            <?php
        }
        if ($isbonfonctionnement !=0) {
            ?>
                <div class="row">
                  <div class="col-md-12">
                    <h4 style="margin:5px 0px;font-weight: 500;border: 1px solid #616161;padding: 10px;font-size: 12px;
                          background: #616161;color: white;">Date denieres verification de bon fonctionnement
                    </h4>
                  </div>
                </div>
                <?php
                    foreach ($array[1] as $key => $value) {
                        ?>
                            <div class="row" style="padding: 0px 10px;">
                                <div class="col-md-12">
                                  <?=date('d-m-Y',strtotime($value->date_prevue))?>
                                </div>
                                <div class="col-md-12">
                                  <h4>Société,utilisateur ou presonne compétente / organisme agrée</h4>
                                </div>
                                <div class="col-md-12">
                                  <?=$value->fournisseur?>
                                </div>
                                <div class="col-md-12">
                                  <h4>Date prochaine vérification probable</h4>
                                </div>
                                <div class="col-md-12">
                                  <label style="color:red;font-weight: 700" id="pverificationavant"><?=date('d-m-Y',strtotime($value->date_reel))?></label>
                                </div>
                            </div>
                        <?php
                    }
                ?>
            <?php
        }
        ?>  
        <div class="row">
          <div class="col-md-12">
            <h4 style="margin:5px 0px;font-weight: 500;border: 1px solid #616161;padding: 10px;font-size: 12px;
                      background: #616161;color: white;" (click)="setvaleur(4)">Date Maintenance
              <ion-icon name="caret-forward-outline" style="float: right"></ion-icon>
            </h4>
          </div>
        </div>
        <?php
        foreach ($array[2] as $key => $value) {
            ?>
                <div class="row" style="padding: 0px 10px;border-bottom: 1px solid #842e1b">
                  <div class="col-md-12">
                    <?=date('d-m-Y',strtotime($value->date_prevue))?>
                  </div>
                  <div class="col-md-12">
                    <h4>Société,utilisateur ou presonne compétente / organisme agrée</h4>
                  </div>
                  <div class="col-md-12">
                    <?=$value->fournisseur?>
                  </div>
                  <div class="col-md-12">
                    <h4>Date prochaine vérification probable</h4>
                  </div>
                  <div class="col-md-12">
                    <label style="color:red;font-weight: 700" id="pverificationavant"><?=date('d-m-Y',strtotime($value->date_reel))?></label>
                  </div>
                </div>
            <?php
        }
    }
?>