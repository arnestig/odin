<?php
session_start();

include_once('include/html_frame.php');

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
          <div class="modal-body">
            <form>
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
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Add User</button>
          </div>
        </div>
      </div>
    </div>
<!-- Modal ADD USER code end -->

<!-- Modal EDIT USER code start -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Edit user</h4>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="userName">User name</label>
                <input type="text" class="form-control" id="userName" placeholder="User name" value="userNBR">
              </div>
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Name" value="John Doe">
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Email" value="john.doe@email.com"></input>
              </div>
              <div class="form-group">
                <button type="button" class="btn btn-primary">Generate and mail new password</button>
              </div>
              <div class="form-group">
                <input type="checkbox"> Check to give user Admin-privileges</input>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
<!-- Modal EDIT USER code end -->

<!-- Modal DELETE USER code start -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Delete user</h4>
          </div>
          <div class="modal-body">
            <form>
              <div class="col-lg-6">
                <p>User Name:</p>
              </div>
              <div class="col-lg-6">
                <p>userNBR</p>
              </div>
              <div class="col-lg-6">
                <p>Name:</p>
              </div>
              <div class="col-lg-6">
                <p>John Doe</p>
              </div>
              <div class="col-lg-6">
                <p>Email:</p>
              </div>
              <div class="col-lg-6">
                <p>john.doe@email.com</p>
              </div>
              <div class="form-group">
                <label for="messageToUser">Message to user</label>
                <textarea class="form-control" rows="3" id="messageToUser" placeholder="Write message here"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Delete user</button>
          </div>
        </div>
      </div>
    </div>
<!-- Modal DELETE USER code end -->
';

$frame->doc_nav("Users", $_SESSION[ 'username' ] );

echo '
    <div class="container">
      <div class="row">
        <div class="col-lg-offset-1 col-lg-8">
          
          <div class="row">
            <div class="col-lg-12">
              <h3>Manage Users <i class="glyphicon glyphicon-user"></i></h3>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12"><a href="" data-toggle="modal" data-target="#addUserModal">
                <p><i class="glyphicon glyphicon-plus"></i>Add user</p>
              </a></div>
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

                 <!-- tr*20>td{user$}+td{John}+td{Doe}+td{john.doe@email.com}+(td>a[data-toggle="modal"][data-target="#editUserModal"]>i.glyphicon.glyphicon-pencil)+(td>a[data-toggle="modal"][data-target="#deleteUserModal"]>i.glyphicon.glyphicon-trash) -->

                  <tr>
                    <td>user1</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user2</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user3</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user4</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user5</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user6</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user7</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user8</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user9</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user10</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user11</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user12</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user13</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user14</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user15</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user16</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user17</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user18</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user19</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  <tr>
                    <td>user20</td>
                    <td>John</td>
                    <td>Doe</td>
                    <td>john.doe@email.com</td>
                    <td><a href="" data-toggle="modal" data-target="#editUserModal"><i class="glyphicon glyphicon-pencil"></i></a></td>
                    <td><a href="" data-toggle="modal" data-target="#deleteUserModal"><i class="glyphicon glyphicon-trash"></i></a></td>
                  </tr>
                  
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