<?php
session_start();

include_once('include/html_frame.php');
include_once('include/usermanagement.php');
include_once('include/mail_handler.php');
include_once('include/settings.php');


$userManager = new UserManagement();
$mailHandler = new MailHandler();
$settings = new Settings();

//These are being set after a post action (alert types: success/warning)
$alert_message = '';
$alert_type = '';

// Sanitize, validate, crosscheck, confirm and ask pretty...
if (isset( $_POST[ 'add_user' ] )) {
  // TODO: Change pwd-gen to something safe and useful
  $not_very_rnd_pwd = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10 );
  $alert_message = 'Server generated pwd is <strong>'.$not_very_rnd_pwd.'</strong>. Testing only. Remove this alert and mail to user instead.';
  $alert_type = 'warning';
  //Generate and mail password
  if ( $_POST[ 'add_user' ] === 'Add user' ) {
    $userManager->addUser(
        $_POST[ 'userName' ],
        $not_very_rnd_pwd, 
        1, //<- Server has set the pwd
        $_POST[ 'firstName' ],
        $_POST[ 'lastName' ],
        $_POST[ 'email' ]
    );
  }    
}

if (isset( $_POST[ 'edit_user' ] )) {
  // update values in model IF clean input
  //WARNING!! fulhack below updateUser>pw set to blank!!
  $userManager->adminUpdateUser(
    $_POST[ 'userId' ],
    $_POST[ 'userName' ],
    $_POST[ 'firstName' ],
    $_POST[ 'lastName' ],
    $_POST[ 'email' ]
  );

// TODO: Implement admin functionality
  if (isset( $_POST[ 'adminPrivileges' ])) {
    // Warning! Make admin is not a function
    // do stuff if admin is set...
    $userManager->makeAdmin(
      $_POST[ 'userId' ],
      $_POST[ 'userName' ],
      $_POST[ 'firstName' ],
      $_POST[ 'lastName' ],
      $_POST[ 'email' ],
      $_POST[ 'adminPrivileges' ]
    );
  }
  // generate confirmation
  $alert_message = 'Profile info for user <strong>'.$_POST['userName'].'</strong> was successfully updated.';
  $alert_type = 'success';
}

// TODO: MAIL instead. No pwd actually is mailed or changed atm
if (isset( $_POST[ 'generate_new_password' ] )) {
  $not_very_rnd_pwd = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10 );
  
  $userManager->updateUser(
    $_POST[ 'userId' ],
    $_POST[ 'userName' ],
    $not_very_rnd_pwd,
    1,
    $_POST[ 'firstName' ],
    $_POST[ 'lastName' ],
    $_POST[ 'email' ]
  );

  $alert_message = 'A new password: '.$not_very_rnd_pwd.' was generated for <strong>'.$_POST['userName'].'</strong> and sent to '.$_POST['email'];
  $alert_type = 'warning';
}

if (isset( $_POST[ 'delete_user' ])) {
  // TODO: Get and save mailaddress before rm user
  $id = intval($_POST[ 'userId' ]);
  $user_info = $userManager->getUserInfo( $id );
  if ($id>0) $userManager->removeUser( $id );
  // Notify user of deletion or not?
  if (false) {
    $settings->getSenderMail();
    $mailHandler->sendMail();
  }
  $alert_message = 'User <strong>'.$user_info[ 'usr_usern' ].'</strong> was successfully deleted.';
  $alert_type = 'success';
}

$alert_html = '';
if ($alert_message != '' && $alert_type != '') {
  $alert_html = '<div class="alert alert-'.$alert_type.' fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            '.$alert_message.'
          </div>';
}

generate_data();

// Redundant...
function generate_data() {
  $userManager = new UserManagement();
  $_SESSION[ 'users' ] = $userManager->getUsers();
}

function generate_user_list() {
  $userlist = '';
  foreach ( $_SESSION[ 'users' ] as $row ) {
    $userlist .= '
                  <tr>
                    <td>'.$row[ 'usr_usern' ].'</td>
                    <td>'.$row[ 'usr_firstn' ].'</td>
                    <td>'.$row[ 'usr_lastn' ].'</td>
                    <td>'.$row[ 'usr_email' ].'</td>

                    <td><a class="open-EditUserDialog" data-userid="'.$row[ 'usr_id' ].'" data-username="'.$row[ 'usr_usern' ].'" data-firstname="'.$row[ 'usr_firstn' ].'" data-lastname="'.$row[ 'usr_lastn' ].'" data-email="'.$row[ 'usr_email' ].'" href="#editUserDialog" data-toggle="modal" data-backdrop="static"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a class="open-RemoveUserDialog" data-userid="'.$row[ 'usr_id' ].'" data-username="'.$row[ 'usr_usern' ].'" data-firstname="'.$row[ 'usr_firstn' ].'" data-lastname="'.$row[ 'usr_lastn' ].'" data-email="'.$row[ 'usr_email' ].'" href="#removeUserDialog" data-toggle="modal" data-backdrop="static"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
    ';
  }
  return $userlist;
}

$frame = new HTMLframe();
$frame->doc_start("Manage Users");

echo '
<!-- Modal ADD USER code start -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Add user</h4>
          </div>
          <form>
          <div class="modal-body">
            
            <div class="form-group col-lg-12">
              <p>When adding a new user in this modal, a password will be generated and mailed with other information about the users account to the user.</p>
            </div>
            <div class="form-group col-lg-12">
              <label for="userName">Username</label>
              <input type="text" class="form-control" id="userName" placeholder="Username">
            </div>
            <div class="form-group col-lg-6">
              <label for="firstName">First name</label>
              <input type="text" class="form-control" id="firstName" placeholder="First name">
            </div>
            <div class="form-group col-lg-6">
              <label for="lastName">Last name</label>
              <input type="text" class="form-control" id="lastName" placeholder="First name">
            </div>
            <div class="form-group col-lg-12">
              <label for="email">Email</label>
              <input type="email" class="form-control" id="email" placeholder="Email"></input>
            </div>
        
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <input type="submit" class="btn btn-primary" name="add_user" value="Add User">
          </div>
          </form>
        </div>
      </div>
    </div>
<!-- Modal ADD USER code end -->

<!-- Modal EDIT USER code start -->
    <div class="modal fade" id="editUserDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Edit user</h4>
          </div>
          <form action="manage_users.php" method="POST">
          <div class="modal-body">
            
              <div class="form-group">
                <input type="hidden" class="form-control" id="userId" name="userId" value=""/>
                <label for="userName">Username</label>
                <input type="text" class="form-control" id="userName" name="userName" placeholder="Username" value=""/>
              </div>
              <div class="form-group">
                <label for="firstName">First name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First name" value=""/>
              </div>
              <div class="form-group">
                <label for="lastName">Last name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last name" value=""/>
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value=""/>
              </div>
              <div class="form-group">
                <input type="submit" name="generate_new_password" value="Generate and mail new password" class="btn btn-primary"/>
              </div>
              <div class="form-group">
                <input type="checkbox" name="adminPrivileges" checked> Check to give user Admin-privileges</input>
              </div>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <input type="submit" name="edit_user" value="Save changes" class="btn btn-primary">
          </div>
          </form>
        </div>
      </div>
    </div>
<!-- Modal EDIT USER code end -->

<!-- Modal DELETE USER code start -->
    <div class="modal fade" id="removeUserDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Delete user</h4>
          </div>
          <form class="form-horizontal" method="POST" action="manage_users.php">
          <div class="modal-body">
            
              
              <div class="form-group">
                <label for="userName" class="col-lg-6">Username</label>
                <input class="form-control" type="text" id="userName" name="userName" value="" disabled>
                <input class="form-control" type="hidden" id="userId" name="userId" value="">
              </div>
              <div class="form-group">
                <label for="firstName" class="col-lg-6">First name</label>
                <input class="form-control" type="text" id="firstName" value="" disabled>
              </div>
              <div class="form-group">
                <label for="lastName" class="col-lg-6">Last name</label>
                <input class="form-control" type="text" id="lastName" value="" disabled>
              </div>
              <div class="form-group">
                <label for="email" class="col-lg-6">Email</label>
                <input class="form-control" type="email" id="email" value="" disabled>
              </div>
              <div class="form-group" class="col-lg-12">
                <label for="messageToUser">Message to user</label>
                <textarea type="text" class="form-control col-lg-12" rows="3" name="messageToUser" id="messageToUser" placeholder="If left empty a default message will be sent."></textarea>
              </div>
          
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <input type="submit" name="delete_user" value="Delete user" class="btn btn-primary">
          </div>
          </form>
        </div>
      </div>
    </div>
<!-- Modal DELETE USER code end -->
';

$frame->doc_nav("Users", $_SESSION[ 'user_data' ][ 'usr_usern' ] );

echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-1 col-lg-8">
          
          '.$alert_html.'

          <div class="row">
            <div class="col-lg-12">
              <h3>Manage Users <i class="glyphicon glyphicon-user"></i></h3>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12"><a href="" data-toggle="modal" data-target="#addUserModal">
                <p><i class="glyphicon glyphicon-plus"></i>Add user</p>
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Edit</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                '.generate_user_list().'
                </tbody>
              </table>
            </div>
          </div>
        </div>
          

      </div>

    </div>
';

$frame->doc_end();

?>