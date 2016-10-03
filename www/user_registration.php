<?php

/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2016  Tobias Eliasson <arnestig@gmail.com>
                            Jonas Berglund <jonas.jberglund@gmail.com>
                            Martin Rydin <martin.rydin@gmail.com>

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License along
   with this program; if not, write to the Free Software Foundation, Inc.,
   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

*/

session_start();

include_once('include/html_frame.php');
include_once('include/usermanagement.php');
include_once('include/user.php');
include_once('include/settings.php');

$settings = new Settings();
$user_reg = $settings->getSettingValue('allow_user_registration');
if ($user_reg != 'checked') {
  header('Location: index.php');
}

$alert_msg = '';

if (!empty($_POST['register'])) {
  if (empty($_POST['terms_and_conditions'])) {
    $alert_msg = 'You have to agree to the terms and conditions before continuing.';
  } else if (!empty($_POST['reg_password']) && 
              !empty($_POST['reg_password_repeat']) && 
              $_POST['reg_password'] !== $_POST['reg_password_repeat']) {
    $alert_msg = 'Make sure you type the same password twice.';
    unset($_POST['reg_password']);
    unset($_POST['reg_password_repeat']);
  } else if ( empty($_POST['reg_username']) || 
              empty($_POST['reg_password']) ||
              empty($_POST['reg_password_repeat']) ||
              empty($_POST['reg_first_name']) ||
              empty($_POST['reg_last_name']) ||
              empty($_POST['reg_email']) ||
              empty($_POST['terms_and_conditions'])) {
    $alert_msg = 'You must fill out all the fields.';
  } else if ( strpos( $_POST['reg_username'], ' ') !== false ) {
    $alert_msg = 'Your username cannot contain white space.';
  } else {
    $user_man = new UserManagement();
    $userHandler = new User();
    try {
      $user_man->addUser(
        $_POST[ 'reg_username' ],
        $_POST[ 'reg_password' ],
        0,
        $_POST[ 'reg_first_name' ],
        $_POST[ 'reg_last_name' ],
        $_POST[ 'reg_email' ]);
      if ($userHandler->login($_POST[ 'reg_username' ],$_POST[ 'reg_password' ])) {
        header('Location: overview.php');
      }
    } catch (PDOException $e) {
      $alert_msg = 'Your registration could not be completed because the desired username is already taken.';
    }
  }
}

function alert($alert_msg) {
  if ($alert_msg !== '') {
    return '<div class="row">
              <div class="col-lg-12 alert alert-danger">
                '.$alert_msg.'
              </div>
            </div>';
  }
}

$frame = new HTMLframe();
$frame->doc_start("Register User");

echo '
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="logo.php?small" alt="Odin - Logo"></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">About ODIN</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-lg-12"><br></div>
      </div>

      <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
          <form class="form" method="POST" action="user_registration.php">
            <div class="row">
              <div class="col-lg-12">
                <h2>Create an account</h2>
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 bg-light">
                <h5>Login details</h5>
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 form-group">
                <label for="inputUserName">Username</label>
                <input type="text" name="reg_username" class="form-control" id="inputUserName" placeholder="Your desired username" value="'.$_POST['reg_username'].'" required pattern="^(?!\s*$).+">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 form-group">
                <label for="inputPassword">Password</label>
                <input type="password" name="reg_password" class="form-control" id="inputPassword" placeholder="Password" value="'.$_POST['reg_password'].'">
              </div>
              <div class="col-lg-6 form-group">
                <label for="inputPasswordRepeat">Repeat Password</label>
                <input type="password" name="reg_password_repeat" class="form-control" id="inputPasswordRepeat" placeholder="Password" value="'.$_POST['reg_password_repeat'].'">
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 bg-light">
                <h5>User information</h5>
              </div>
            </div>
            <div class="row spacer-row"></div>
            <div class="row">
              <div class="col-lg-12 form-group">
                <label for="inputEmail">Email address</label>
                <input type="email" name="reg_email" class="form-control" id="inputEmail" placeholder="Email" value="'.$_POST['reg_email'].'">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 form-group">
                <label for="inputFirstName">First name</label>
                <input type="text" name="reg_first_name" class="form-control" id="inputFirstName" placeholder="First name" value="'.$_POST['reg_first_name'].'" required pattern="^(?!\s*$).+">
              </div>
              <div class="col-lg-6 form-group">
                <label for="inputLastName">Last name</label>
                <input type="text" name="reg_last_name" class="form-control" id="inputLastName" placeholder="Last name" value="'.$_POST['reg_last_name'].'" required pattern="^(?!\s*$).+">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-1 form-group">
                <input type="checkbox" name="terms_and_conditions" class="form-control" style="width: auto; height: auto;">
              </div>
              <div class="col-lg-11 form-group">
                <p>I have read, understood and agree to these <a href="#" data-toggle="modal" data-target="#termsAndConditionsModal">terms and conditions</a></p>
              </div>
            </div>
            '.alert($alert_msg).'
            <div class="row">
              <div class="col-lg-12 form-group">
                <input type="submit" name="register" value="Register and log in" class="btn btn-info form-control"/>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="termsAndConditionsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h2>Terms and conditions</h2>
          </div>
          <div class="modal-body" id="t-o-c">
            <ol>
              <li>Introduction
                <ol>
                  <li>
                    These terms and conditions shall govern your use of our website.
                  </li>
                  <li>
                    By using our website, you accept these terms and conditions in full; accordingly, 
                    if you disagree with these terms and conditions or any part of 
                    these terms and conditions, you must not use our website.
                  </li>
                  <li>
                    If you [register with our website, submit any material to our website or use 
                    any of our website services], we will ask you to expressly agree to these terms 
                    and conditions.
                    </li>
                  <li>
                    You must be at least 18 years of age to use our website; by using our 
                    website or agreeing to these terms and conditions, you warrant and represent 
                    to us that you are at least 18 years of age.
                  </li>
                  <li>
                    Our website uses cookies; by using our website or agreeing to these terms and conditions, 
                    you consent to our use of cookies in accordance with the terms of our [privacy 
                    and cookies policy].
                  </li>
                </ol>
              </li>
              <li>Credit
                <ol>
                  <li>
                    This document was created using a template from SEQ Legal (http://www.seqlegal.com).
                  </li>
                </ol>
              </li>
              <li>Copyright notice
                <ol>
                  <li>
                    Copyright (C) 2015-2016  Tobias Eliasson, Jonas Berglund, Martin Rydin
                  </li>
                  <li>Subject to the express provisions of these terms and conditions:
                    <ol>
                      <li>
                        we, together with our licensors, own and control all the copyright and 
                        other intellectual property rights in our website and the material on our 
                        website; and
                      </li>
                      <li>
                        all the copyright and other intellectual property rights in our website 
                        and the material on our website are reserved.</li>
                    </ol>
                  </li>
                </ol>
              </li>
              <li>Licence to use website
                <ol>
                  <li>You may:
                    <ol>
                      <li>
                        view pages from our website in a web browser;
                      </li>
                      <li>
                        download pages from our website for caching in a web browser;
                      </li>
                      <li>
                        print pages from our website;
                      </li>
                      <li>
                        use our website services by means of a web browser,
                      </li>
                    </ol>
                    subject to the other provisions of these terms and conditions.
                  </li>
                  <li>
                    Except as expressly permitted by Section 4.1 or the other provisions of these 
                    terms and conditions, you must not download any material from our website 
                    or save any such material to your computer.
                  </li>
                  <li>Unless you own or control the relevant rights in the material, you must not:
                    <ol>
                      <li>
                        republish material from our website (including republication on another website);
                      </li>
                      <li>
                        sell, rent or sub-license material from our website;
                      </li>
                      <li>
                        show any material from our website in public;
                      </li>
                      <li>
                        exploit material from our website for a commercial purpose; or
                      </li>
                      <li>
                        redistribute material from our website.
                      </li>
                    </ol>
                  </li>
                  <li>
                    We reserve the right to restrict access to areas of our website, or indeed our 
                    whole website, at our discretion; you must not circumvent or bypass, or attempt to 
                    circumvent or bypass, any access restriction measures on our website.
                  </li>
                </ol>
              </li>
              <li>Acceptable use
                <ol>
                  <li>You must not:
                    <ol>
                      <li>
                        use our website in any way or take any action that causes, or may cause, damage to 
                        the website or impairment of the performance, availability or accessibility of 
                        the website;
                      </li>
                      <li>
                        use our website in any way that is unlawful, illegal, fraudulent or harmful, or 
                        in connection with any unlawful, illegal, fraudulent or harmful purpose or activity;
                      </li>
                      <li>
                        use our website to copy, store, host, transmit, send, use, publish or distribute any 
                        material which consists of (or is linked to) any spyware, computer virus, Trojan horse, 
                        worm, keystroke logger, rootkit or other malicious computer software;
                      </li>
                      <li>
                        conduct any systematic or automated data collection activities (including without 
                        limitation scraping, data mining, data extraction and data harvesting) on or in 
                        relation to our website without our expresswritten consent;
                      </li>
                      <li>
                        access or otherwise interact with our website using any robot, spider or other 
                        automated means;
                      </li>
                      <li>
                        violate the directives set out in the robots.txt file for our website; or
                      </li>
                      <li>
                        use data collected from our website for any direct marketing activity (including
                        without limitation email marketing, SMS marketing, telemarketing and direct mailing).
                      </li>
                    </ol>
                  </li>
                  <li>
                    You must not use data collected from our website to contact individuals, companies or 
                    other persons or entities.
                  </li>
                  <li>
                    You must ensure that all the information you supply to us through our website, or in 
                    relation to our website, is true, accurate, current, complete and non-misleading.
                  </li>
                </ol>
              </li>
              <li>Registration and accounts
                <ol>
                  <li>To be eligible for an individual account on our website under this Section 6, you must 
                    be at least 18 years of age.
                  </li>
                  <li>
                    You may register for an account with our website by completing and submitting the account 
                    registration form on our website, or by a request for an account to this websites administrators.
                  </li>
                  <li>
                    You must not allow any other person to use your account to access the website.
                  </li>
                  <li>
                    You must notify us in writing immediately if you become aware of any unauthorised use of 
                    your account.
                  </li>
                  <li>
                    You must not use any other persons account to access the website.
                  </li>
                </ol>
              </li>
              <li>User login details
                <ol>
                  <li>
                    If you register for an account with our website, we will provide you with OR you will be asked
                    to choose a user ID and password.
                  </li>
                  <li>
                    Your user ID must not be liable to mislead and must comply with the content 
                    rules set out in Section 10; you must not use your account or user ID for or in connection 
                    with the impersonation of any person.
                  </li>
                  <li>
                    You must keep your password confidential.
                  </li>
                  <li>
                    You must notify us in writing immediately if you become aware of any disclosure of your password.
                  </li>
                  <li>
                    You are responsible for any activity on our website arising out of any failure to keep 
                    your password confidential, and may be held liable for any losses arising out of such a failure.
                  </li>
                </ol>
              </li>
              <li>Cancellation and suspension of account
                <ol>
                  <li>We may:
                    <ol>
                      <li>
                        suspend your account;
                      </li>
                      <li>
                        cancel your account; and/or
                      </li>
                      <li>
                        edit your account details;
                      </li>
                    </ol>
                    at any time in our sole discretion without notice or explanation.
                  </li>
                  <li>
                    You may cancel your account on our website by contacting our administrator(s).
                  </li>
                </ol>
              </li>
              <li>Your content: licence
                <ol>
                  <li>
                    In these terms and conditions, "your content" means all works and materials 
                    (including without limitation text, graphics, images, audio material, video 
                      material, audio-visual material, scripts, software and files) that you submit to
                      us or our website for storage or publication on, processing by, or transmission 
                      via, our website.
                  </li>
                  <li>
                    You grant to us a worldwide, irrevocable, non-exclusive, royalty-free licence to use, 
                    reproduce, store, adapt, publish, translate and distribute your content in any existing 
                    or future media] OR [reproduce, store and publish your content on and in relation to 
                    this website and any successor website] OR [reproduce, store and, with your specific consent, 
                    publish your content on and in relation to this website].
                  </li>
                  <li>
                    You grant to us the right to sub-license the rights licensed under Section 9.2.
                  </li>
                  <li>
                    You grant to us the right to bring an action for infringement of the rights licensed 
                    under Section 9.2.
                  </li>
                  <li>
                    You hereby waive all your moral rights in your content to the maximum extent 
                    permitted by applicable law; and you warrant and represent that all other moral rights 
                    in your content have been waived to the maximum extent permitted by applicable law.
                  </li>
                  <li>
                    You may edit your content to the extent permitted using the editing functionality made 
                    available on our website.
                  </li>
                  <li>
                    Without prejudice to our other rights under these terms and conditions, if you breach any 
                    provision of these terms and conditions in any way, or if we reasonably suspect that you have 
                    breached these terms and conditions in any way, we may delete, unpublish or edit any or all 
                    of your content.
                  </li>
                </ol>
              </li>
              <li>You content: rules
                <ol>
                  <li>
                    You warrant and represent that your content will comply with these terms and conditions.
                  </li>
                  <li>
                  Your content must not be illegal or unlawful, must not infringe any persons legal rights, 
                  and must not be capable of giving rise to legal action against any person (in each case 
                    in any jurisdiction and under any applicable law).
                  </li>
                  <li>Your content, and the use of your content by us in accordance with these terms 
                  and conditions, must not:
                    <ol>
                      <li>
                        be libellous or maliciously false;
                      </li>
                      <li>
                        be obscene or indecent;
                      </li>
                      <li>
                        infringe any copyright, moral right, database right, trade mark right, 
                        design right, right in passing off, or other intellectual property right;
                      </li>
                      <li>
                        infringe any right of confidence, right of privacy or right under data protection 
                        legislation;
                      </li>
                      <li>
                        constitute negligent advice or contain any negligent statement;
                      </li>
                      <li>
                        constitute an incitement to commit a crime, instructions for the commission of 
                        a crime or the promotion of criminal activity;
                      </li>
                      <li>
                        be in contempt of any court, or in breach of any court order;
                      </li>
                      <li>
                        be in breach of racial or religious hatred or discrimination legislation;
                      </li>
                      <li>
                        be blasphemous;
                      </li>
                      <li>
                        be in breach of official secrets legislation;
                      </li>
                      <li>
                        be in breach of any contractual obligation owed to any person;
                      </li>
                      <li>
                        depict violence in an explicit, graphic or gratuitous manner;
                      </li>
                      <li>
                        be pornographic, lewd, suggestive or sexually explicit;
                      </li>
                      <li>
                        be untrue, false, inaccurate or misleading;
                      </li>
                      <li>
                        consist of or contain any instructions, advice or other information which may 
                        be acted upon and could, if acted upon, cause illness, injury or death, or 
                        any other loss or damage;
                      </li>
                      <li>
                        constitute spam;
                      </li>
                      <li>
                        be offensive, deceptive, fraudulent, threatening, abusive, harassing, anti-social, 
                        menacing, hateful, discriminatory or inflammatory; or
                      </li>
                      <li>
                        cause annoyance, inconvenience or needless anxiety to any person.
                      </li>
                    </ol>
                  </li>
                </ol>
              </li>
              <li>Limited warranties
                <ol>
                  <li>We do not warrant or represent:
                    <ol>
                      <li>
                        the completeness or accuracy of the information published on our website;
                      </li>
                      <li>
                        that the material on the website is up to date; or
                      </li>
                      <li>
                        that the website or any service on the website will remain available.
                      </li>
                    </ol>
                  </li>
                  <li>
                    We reserve the right to discontinue or alter any or all of our website services, and to 
                    stop publishing our website, at any time in our sole discretion without notice or 
                    explanation; and save to the extent expressly provided otherwise in these terms and 
                    conditions, you will not be entitled to any compensation or other payment upon the 
                    discontinuance or alteration of any website services, or if we stop publishing the website.
                  </li>
                  <li>
                    To the maximum extent permitted by applicable law and subject to Section 12.1, we exclude 
                    all representations and warranties relating to the subject matter of these terms 
                    and conditions, our website and the use of our website.
                  </li>
                </ol>
              </li>
              <li>Limitations and exclusions of liability
                <ol>
                  <li>Nothing in these terms and conditions will:
                    <ol>
                      <li>
                        limit or exclude any liability for death or personal injury resulting from negligence;
                      </li>
                      <li>
                        limit or exclude any liability for fraud or fraudulent misrepresentation;
                      </li>
                      <li>
                        limit any liabilities in any way that is not permitted under applicable law; or
                      </li>
                      <li>
                        exclude any liabilities that may not be excluded under applicable law.
                      </li>
                    </ol>
                  </li>

                  <li>The limitations and exclusions of liability set out in this Section 12 and 
                  elsewhere in these terms and conditions:
                    <ol>
                      <li>
                        are subject to Section 12.1; and
                      </li>
                      <li>
                        govern all liabilities arising under these terms and conditions or relating to the 
                        subject matter of these terms and conditions, including liabilities arising in 
                        contract, in tort (including negligence) and for breach of statutory duty, except 
                        to the extent expressly provided otherwise in these terms and conditions.
                      </li>
                    </ol>
                  </li>
                  <li>
                    To the extent that our website and the information and services on our website are 
                    provided free of charge, we will not be liable for any loss or damage of any nature.
                  </li>
                  <li>
                    We will not be liable to you in respect of any losses arising out of any event or 
                    events beyond our reasonable control.
                  </li>
                  <li>
                    We will not be liable to you in respect of any business losses, including (without 
                    limitation) loss of or damage to profits, income, revenue, use, production, 
                    anticipated savings, business, contracts, commercial opportunities or goodwill.
                  </li>
                  <li>
                    We will not be liable to you in respect of any loss or corruption of any data, database 
                    or software.
                  </li>
                  <li>
                    We will not be liable to you in respect of any special, indirect or consequential loss or damage.
                  </li>
                  <li>
                    You accept that we have an interest in limiting the personal liability of our officers 
                    and employees and, having regard to that interest, you acknowledge that we are a limited 
                    liability entity; you agree that you will not bring any claim personally against our 
                    officers or employees in respect of any losses you suffer in connection with the website 
                    or these terms and conditions (this will not, of course, limit or exclude the liability 
                    of the limited liability entity itself for the acts and omissions of our officers and employees).
                  </li>
                </ol>
              </li>
              <li>Breaches of these terms and conditions
                <ol>
                  <li>Without prejudice to our other rights under these terms and conditions, if you breach 
                  these terms and conditions in any way, or if we reasonably suspect that you have 
                  breached these terms and conditions in any way, we may:
                    <ol>
                      <li>
                        send you one or more formal warnings;
                      </li>
                      <li>
                        temporarily suspend your access to our website;
                      </li>
                      <li>
                        permanently prohibit you from accessing our website;
                      </li>
                      <li>
                        block computers using your IP address from accessing our website;
                      </li>
                      <li>
                        contact any or all of your internet service providers and request that they block 
                        your access to our website;
                      </li>
                      <li>
                        commence legal action against you, whether for breach of contract or otherwise; and/or
                      </li>
                      <li>
                        suspend or delete your account on our website.
                      </li>
                    </ol>
                  </li>
                  <li>
                    Where we suspend or prohibit or block your access to our website or a part of our website, 
                    you must not take any action to circumvent such suspension or prohibition or blocking 
                    (including without limitation creating and/or using a different account).
                  </li>
                </ol>
              </li>
              <li>Variation
                <ol>
                  <li>
                    We may revise these terms and conditions from time to time.
                  </li>
                  <li>
                    The revised terms and conditions shall apply to the use of our website from the 
                    date of publication of the revised terms and conditions on the website, and you 
                    hereby waive any right you may otherwise have to be notified of, or to consent 
                    to, revisions of these terms and conditions. OR We will give you written 
                    notice of any revision of these terms and conditions, and the revised terms and 
                    conditions will apply to the use of our website from the date that we give you 
                    such notice; if you do not agree to the revised terms and conditions, you must 
                    stop using our website.
                  </li>
                  <li>
                    If you have given your express agreement to these terms and conditions, we will ask 
                    for your express agreement to any revision of these terms and conditions; and if 
                    you do not give your express agreement to the revised terms and conditions within 
                    such period as we may specify, we will disable or delete your account on the website, 
                    and you must stop using the website.
                  </li>
                </ol>
              </li>
              <li>Assignment
                <ol>
                  <li>
                    You hereby agree that we may assign, transfer, sub-contract or otherwise deal with 
                    our rights and/or obligations under these terms and conditions.
                  </li>
                  <li>
                    You may not without our prior written consent assign, transfer, sub-contract or 
                    otherwise deal with any of your rights and/or obligations under these terms and 
                    conditions.
                  </li>
                </ol>
              </li>
              <li>Severability
                <ol>
                  <li>
                    If a provision of these terms and conditions is determined by any court or other 
                    competent authority to be unlawful and/or unenforceable, the other provisions will 
                    continue in effect.
                  </li>
                  <li>
                    If any unlawful and/or unenforceable provision of these terms and conditions would 
                    be lawful or enforceable if part of it were deleted, that part will be deemed to be 
                    deleted, and the rest of the provision will continue in effect.
                  </li>
                </ol>
              </li>
              <li>Third party rights
                <ol>
                  <li>
                    A contract under these terms and conditions is for our benefit and your benefit, and 
                    is not intended to benefit or be enforceable by any third party.
                  </li>
                  <li>
                    The exercise of the parties rights under a contract under these terms and conditions 
                    is not subject to the consent of any third party.
                  </li>
                </ol>
              </li>
              <li>Entire agreement
                <ol>
                  <li>
                    Subject to Section 12.1, these terms and conditions, together with our privacy and 
                    cookies policy, shall constitute the entire agreement between you and us in relation 
                    to your use of our website and shall supersede all previous agreements between you 
                    and us in relation to your use of our website.
                  </li>
                </ol>
              </li>
              <li>Law and jurisdiction
                <ol>
                  <li>
                    These terms and conditions shall be governed by and construed in accordance with the law
                    of the country where the server implementing ODIN is located.
                  </li>
                  <li>
                    Any disputes relating to these terms and conditions shall be subject to the jurisdiction 
                    of the courts of the country where the server implementing ODIN is located.
                  </li>
                </ol>
              </li>
              <li>Our details
                <ol>
                  This website is owned and operated by {{ individual/organisation from settings }}.
                </ol>
              </li>
            </ol>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
';

$frame->doc_end();

?>
