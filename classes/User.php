<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 18/02/16
 * Time: 15:18
 */

class User {
    protected $id;
    protected $username;
    protected $user_pass;
    protected $user_email;
    protected $user_registered;
    protected $last_login;
    protected $active;
    protected $deleted;
    protected $deleted_on;
    protected $admin = 0;
    protected $userstore = 0; // Usuario de tienda
    protected $storeid = 0;
    protected $userrepository = 0;
    protected $useraccounting = 0;
    protected $usermanager;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUserPass()
    {
        return $this->user_pass;
    }

    /**
     * @param mixed $user_pass
     */
    public function setUserPass($user_pass)
    {
        $this->user_pass = $user_pass;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @param mixed $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
    }

    /**
     * @return mixed
     */
    public function getUserRegistered()
    {
        return $this->user_registered;
    }

    /**
     * @param mixed $user_registered
     */
    public function setUserRegistered($user_registered)
    {
        $this->user_registered = $user_registered;
    }

    /**
     * @return mixed
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param mixed $last_login
     */
    public function setLastLogin($last_login)
    {
        $this->last_login = $last_login;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getDeletedOn()
    {
        return $this->deleted_on;
    }

    /**
     * @param mixed $deleted_on
     */
    public function setDeletedOn($deleted_on)
    {
        $this->deleted_on = $deleted_on;
    }

    /**
     * @return int
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param int $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return int
     */
    public function getUserstore()
    {
        return $this->userstore;
    }

    /**
     * @param int $userstore
     */
    public function setUserstore($userstore)
    {
        $this->userstore = $userstore;
    }

    /**
     * @return int
     */
    public function getStoreid()
    {
        return $this->storeid;
    }

    /**
     * @param int $storeid
     */
    public function setStoreid($storeid)
    {
        $this->storeid = $storeid;
    }

    /**
     * @return int
     */
    public function getUserrepository()
    {
        return $this->userrepository;
    }

    /**
     * @param int $userrepository
     */
    public function setUserrepository($userrepository)
    {
        $this->userrepository = $userrepository;
    }

    /**
     * @return mixed
     */
    public function getUsermanager()
    {
        return $this->usermanager;
    }

    /**
     * @param mixed $usermanager
     */
    public function setUsermanager($usermanager)
    {
        $this->usermanager = $usermanager;
    }

    /**
     * @return mixed
     */
    public function getUseraccounting()
    {
        return $this->useraccounting;
    }

    /**
     * @param mixed $useraccounting
     */
    public function setUseraccounting($useraccounting)
    {
        $this->useraccounting = $useraccounting;
    }

    /**
     * Dejar la función aquí ya que la usa el controlador controller.php, si se pone en UserController fallaría
     */
    function inactiveSessionControl() { // Para uso no wordpress
        $timeout = 3600; // Number of seconds until it times out.
        // Check if the timeout field exists.
        if(isset($_SESSION['timeout'])) {
            // See if the number of seconds since the last
            // visit is larger than the timeout period.
            $duration = time() - (int)$_SESSION['timeout'];
            //echo $duration.'<br>';
            if($duration > $timeout) {
                // Destroy the session and restart it.
                session_destroy();
                unset($_SESSION['user']);
                ?>
                <script>
                    //alert("No hemos detectado actividad en los últimos 60 minutos. Por seguridad hemos cerrado su sesión.");
                    //location.href="< ?php echo $_SERVER['REQUEST_URI']; ?>";
                    location.href = "<?php echo LOGOUT_URL ; ?>";
                </script>
                <?php
            }
        }
        // Update the timout field with the current time.
        $_SESSION['timeout'] = time();
    }
}