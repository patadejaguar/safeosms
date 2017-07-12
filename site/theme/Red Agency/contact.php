<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:      contact.php
* @Package:   GetSimple
* @Action:    Bootstrap3 for GetSimple CMS
*
*****************************************************/

include('header.inc.php');

global $language;
$def_lang=$language."_".strtoupper($language);
if(!isset($def_lang) || empty($def_lang)  || $language=="en") $def_lang="en_US";
//if(count($def_lang)<3) $def_lang = $def_lang.'_'.strtoupper($def_lang);
include(str_replace('\\','/',dirname(__FILE__)).'/lang/'.$def_lang.'.php');

$e_mail = simple_c_default_email();
// Remove all illegal characters from email
$e_mail = filter_var($e_mail, FILTER_SANITIZE_EMAIL);

$Name = '';
$Email = '';
$Subject = '';
$Url = '';
$Body = '';

$HasErrorName = '';
$HasErrorEmail = '';
$HasErrorSubject = '';
$HasErrorBody = '';

$FormError = '';
$MailError = false;
$Success = false;

if (isset($_POST['cmdSendMessage'])) {
  $Name = $_POST['txtName'];
  $Email = $_POST['txtEmail'];
  $Subject = $_POST['txtSubject'];
  $Url = $_POST['txtUrl'];
  $Body = $_POST['txtBody'];
  
  if (empty($Name)) { 
    $HasErrorName = "has-error"; 
    $FormError .= "<li>".$set_lang['MAIL_BAD_NAME']."</li>";
  }
  //if (!check_email_address($Email)) {
  if (filter_var($Email, FILTER_VALIDATE_EMAIL) === false) {
    $HasErrorEmail = "has-error"; 
    $FormError .= "<li>".$set_lang['MAIL_BAD_ADR']."</li>";
  }
  if (empty($Subject)) { 
    $HasErrorSubject = "has-error"; 
    $FormError .= "<li>".$set_lang['MAIL_BAD_SUBJ']."</li>";
  }
  if (empty($Body)) { 
    $HasErrorBody = "has-error"; 
    $FormError .= "<li>".$set_lang['MAIL_BAD_BODY']."</li>";
  }
  
  if (empty($FormError)) {
    if (!empty($Url)) {
      $Subject = "(CONTACT_SPAM) " . $Subject;
      $Body = "Url: $Url\n\n" . $Body;
    }

    if (@mail($e_mail, $Subject, $Body, 'From: "' . str_replace('"', "'", $Name) . '" <' . $Email . '>')) {
      $Success = true;
      
      $Subject = '';
      $Url = '';
      $Body = '';
    } else {
      $MailError = true;
    }
  }
}
?>

	<div class="row">
        <div class="col-md-8">
          
          <?php
            //if (check_email_address($ThemeSettings->ContactEmail)) {
			if (!filter_var($e_mail, FILTER_VALIDATE_EMAIL) === false) {
          ?>
              
              <div class="well">
                <form method="post" class="form-horizontal">
                  <fieldset>
                    <legend><?php get_page_title(); ?></legend>

                    <?php
                      if (!empty($FormError)) {
                    ?>
                    
                        <div class="alert alert-dismissable alert-danger">
                          <button data-dismiss="alert" class="close" type="button">x</button>
                          <?php echo $set_lang['MAIL_ERR_SEND']; ?>
                          <ul>
                            <?php echo $FormError; ?>
                          </ul>
                        </div>

                    <?php
                      } else if ($MailError) {
                    ?>
                    
                        <div class="alert alert-dismissable alert-danger">
                          <button data-dismiss="alert" class="close" type="button">x</button>
                          <?php echo $set_lang['MAIL_ERR_MAIL']; ?>
                        </div>
                        
                    <?php
                      } else if ($Success) {
                    ?>
                    
                        <div class="alert alert-dismissable alert-success">
                          <button data-dismiss="alert" class="close" type="button">x</button>
                          <?php echo $set_lang['MAIL_SEND_SEC']; ?>
                        </div>
                        
                    <?php
                      }
                    ?>
                
                    <div style="font-style:italic;"><?php get_page_content(); ?></div>

                    <div class="form-group <?php echo $HasErrorName; ?>">
                      <label class="col-lg-2 control-label" for="txtName">Name</label>
                      <div class="col-lg-10">
                        <input type="text" id="txtName" name="txtName" class="form-control" value="<?php echo htmlspecialchars($Name, ENT_QUOTES, "UTF-8"); ?>" placeholder="<?php echo $set_lang['MAIL_NAME']; ?>" />
                      </div>
                    </div>

                    <div class="form-group <?php echo $HasErrorEmail; ?>">
                      <label class="col-lg-2 control-label" for="txtEmail">E-mail</label>
                      <div class="col-lg-10">
                        <input type="text" id="txtEmail" name="txtEmail" class="form-control" value="<?php echo htmlentities($Email); ?>" placeholder="<?php echo $set_lang['MAIL_EMAIL']; ?>" />
                      </div>
                    </div>

                    <div class="form-group <?php echo $HasErrorSubject; ?>">
                      <label class="col-lg-2 control-label" for="txtSubject">Subject</label>
                      <div class="col-lg-10">
                        <input type="text" id="txtSubject" name="txtSubject" class="form-control" value="<?php echo htmlspecialchars($Subject, ENT_QUOTES, "UTF-8"); ?>" placeholder="<?php echo $set_lang['MAIL_SUBJ']; ?>" />
                      </div>
                    </div>

                    <div class="form-group" style="display: none;">
                      <label class="col-lg-2 control-label" for="txtUrl">Url</label>
                      <div class="col-lg-10">
                        <input type="text" id="txtUrl" name="txtUrl" class="form-control" value="<?php echo htmlentities($Url); ?>" placeholder="<?php echo $set_lang['MAIL_SUBJ']; ?>" />
                        <span class="help-block">NOTE: Leave this box BLANK!</span>
                      </div>
                    </div>

                    <div class="form-group <?php echo $HasErrorBody; ?>">
                      <label class="col-lg-2 control-label" for="txtBody">Body</label>
                      <div class="col-lg-10">
                        <textarea id="txtBody" name="txtBody" class="form-control" rows="10" placeholder="<?php echo $set_lang['MAIL_BODY']; ?>"><?php echo htmlspecialchars($Body, ENT_QUOTES, "UTF-8"); ?></textarea>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-lg-10 col-lg-offset-2">
                        <button type="submit" id="cmdSendMessage" name="cmdSendMessage" class="btn btn-primary"><?php echo $set_lang['MAIL_SEND']; ?></button>
                      </div>
                    </div>
                  </fieldset>
                </form>
              </div>
                
          <?php
            } else {
          ?>
          
              <div class="alert alert-danger">
                <?php echo $set_lang['MAIL_ERR_ADM']; ?>
              </div> 
              
          <?php
            }
          ?>
            
        </div>
        
        <div class="col-md-4">
          <?php get_component('sidebar'); ?>
        </div>
      </div>

<?php include('footer.inc.php'); ?>
