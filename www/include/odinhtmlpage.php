<?php

include_once( "odinhtml.php" );
include_once( "include/DOMtablegenerator.php" );
include_once( "include/DOMformgenerator.php" );
include_once( "include/usermanagement.php" );
include_once( "include/nwmanagement.php" );
include_once( "include/settings.php" );

class OdinHTMLPage extends OdinHTML
{
    private $POST = false;
    private $GET = false;
    public function __construct()
    {
        $this->loadHTMLFile("include/template.html");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->POST = true;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->GET = true;
        }
    }

    public function getPage()
    {
        if ($this->POST) {
            if ( isset( $_POST[ 'eupSubmit' ] ) ) {
            } elseif ( isset( $_POST[ 'anpSubmit' ] ) ) {
                /* submit received from add network page */
                if ( $_POST[ 'anpSubmit' ] === 'Save' ) {
                    $networkmanagement = new NetworkManagement();
                    $networkmanagement->addNetwork( $_POST[ 'anpNetworkBase' ], $_POST[ 'anpNetworkCIDR' ] );
                }
                $this->displayNetworks();
            } elseif ( isset( $_POST[ 'rnpSubmit' ] ) ) {
            } elseif ( isset( $_POST[ 'rupSubmit' ] ) ) {
            } elseif ( isset( $_POST[ 'dsSubmit' ] ) ) {
                /* submit received from settings page */
                if ( $_POST[ 'dsSubmit' ] === 'Save' ) {
                    $settings = new Settings();
                    $settingsmax = $_POST[ 'dsSettingsIdMax' ];
                    for ( $i = 0; $i < $settingsmax; $i++ ) {
                        $settings->changeSetting( $_POST[ 'dsName'.$i ], $_POST[ 'dsValue'.$i ] );
                    }
                    $this->displaySettings();
                }
            }
        } elseif ($this->GET) {
            if ( isset( $_REQUEST[ 'manage_users' ] ) ) {
                $this->manageUsers();
            } elseif ( isset( $_REQUEST[ 'manage_networks' ] ) ) {
                if ( empty( $_REQUEST[ 'manage_networks' ] ) ) {
                    /* Display a list of our networks */
                    $this->displayNetworks();
                    echo '<br><a href="admin.php?manage_networks=addnetwork">Add network</a>';
                }

                if ( $_REQUEST[ 'manage_networks' ] === 'addnetwork' ) {
                    $this->addNetworkPage();
                }

                if ( $_REQUEST[ 'manage_networks' ] === 'removenetwork' ) {
                    $network_id = $_REQUEST[ 'network_id' ];
                    $this->removeNetworkPage( $network_id );
                }
            } elseif ( isset( $_REQUEST[ 'settings'] ) ) {
                $this->displaySettings();
            } else {
                // Start page
            }
        }
        return ($this->saveHTML());
    }

    private function manageSettings()
    {
        if ( empty( $_REQUEST[ 'settings' ] ) ) {
        } elseif ( $_REQUEST[ 'manage_users' ] === 'adduser' ) {
            editUserPage( "add" );
        } elseif ( $_REQUEST[ 'manage_users' ] === 'edituser' ) {
            $user_id = $_REQUEST[ 'user_id' ];
            editUserPage( "edit", $user_id );
        } elseif ( $_REQUEST[ 'manage_users' ] === 'removeuser' ) {
            $user_id = $_REQUEST[ 'user_id' ];
            removeUserPage( $user_id );
        }
    }

    private function displaySettings()
    {
        $settings = new Settings();
        $allsettings = $settings->getSettings();
        
        $formGenerator = new FormGenerator(
                            $this,
                            'dynamic', # data type
                            'post', # method
                            'index.php?settings' ); #action
        $formGenerator->setDynamicData( $allsettings );
        $formGenerator->setColumnNames( array( 'Setting', 'Value' ) );
        $formGenerator->setCounterName( 'dsSettingsIdMax' );
        $formGenerator->addInput( 'dsName', 'hidden', '', 's_name' );
        $formGenerator->addInput( 'dsValue', 'text', 's_fullname', 's_value' );
        $formGenerator->addSubmit( 'dsSubmit', 'Save', 'Save' );
        $formGenerator->addSubmit( 'dsSubmit', 'Cancel', 'Cancel' );
        $form = $formGenerator->generateHTML();
        $this->getElementById( 'd_content' )->appendChild( $form );
    }

    private function displayNetworks() {
        $networkmanagement = new NetworkManagement();
        $networks = $networkmanagement->getNetworks();

        $tableGenerator = new TableGenerator( $this ); 
        $tableGenerator->addColumn( 'network id', '%d', array( 'nw_id' ) );
        $tableGenerator->addColumn( 'scope', '%s/%d', array( 'nw_base','nw_cidr' ) );
        $tableGenerator->addColumn( '', '<a href="index.php?manage_networks=removenetwork&network_id=%s">remove</a>', array( 'nw_id' ) );
        $tableGenerator->setData( $networks );
        $table = $tableGenerator->generateHTML();
        $this->getElementById( 'd_content' )->appendChild( $table );
        $addNetworkNode = $this->createLink('index.php?manage_networks=addnetwork', 'Add network' );
        $this->getElementById( 'd_content' )->appendChild( $addNetworkNode );
    }

    private function addNetworkPage() {
        $formGenerator = new FormGenerator(
                            $this,
                            'static', # data type
                            'post', # method
                            'index.php?manage_networks' ); #action
        $formGenerator->addStaticData( 'Network', 'text', 'anpNetworkBase' );
        $formGenerator->addStaticData( 'CIDR', 'text', 'anpNetworkCIDR' );
        $formGenerator->setColumnNames( array( '','' ) );
        $formGenerator->addSubmit( 'anpSubmit', 'Save', 'Save' );
        $formGenerator->addSubmit( 'anpSubmit', 'Cancel', 'Cancel' );
        $form = $formGenerator->generateHTML();
        $this->getElementById( 'd_content' )->appendChild( $form );
    }

    private function manageUsers()
    {
        if ( empty( $_REQUEST[ 'manage_users' ] ) ) {
            $this->displayUsersPage();
        } elseif ( $_REQUEST[ 'manage_users' ] === 'adduser' ) {
            editUserPage( "add" );
        } elseif ( $_REQUEST[ 'manage_users' ] === 'edituser' ) {
            $user_id = $_REQUEST[ 'user_id' ];
            editUserPage( "edit", $user_id );
        } elseif ( $_REQUEST[ 'manage_users' ] === 'removeuser' ) {
            $user_id = $_REQUEST[ 'user_id' ];
            removeUserPage( $user_id );
        }
    }

    private function displayUsersPage()
    {
        $usermanagement = new UserManagement();
        $users = $usermanagement->getUsers();

        $url_start = 'index.php?manage_users=';
        $url_edit = '<a href="'.$url_start.'edituser&amp;user_id=%s">edit</a>';
        $url_del = '<a href="'.$url_start.'removeuser&amp;user_id=%s">remove</a>';

        $tableGenerator = new TableGenerator($this);
        $tableGenerator->addColumn( 'user id', '%d', array( 'usr_id' ) );
        $tableGenerator->addColumn( 'username', '%s', array( 'usr_usern' ) );
        $tableGenerator->addColumn( 'name', '%s %s', array( 'usr_firstn','usr_lastn' ) );
        $tableGenerator->addColumn( 'email', '%s', array( 'usr_email' ) );
        $tableGenerator->addColumn( '', $url_edit, array( 'usr_id' ) );
        $tableGenerator->addColumn( '', $url_del, array( 'usr_id' ) );
        $tableGenerator->setData( $users );
        $table = $tableGenerator->generateHTML();
        $this->getElementById('d_content')->appendChild($table);
    }
}

?>
