<?php
namespace backend\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use common\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $repeatpassword;

    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($username, $config = [])
    {
       /* if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Password reset token cannot be blank.');
        }*/
        $this->_user = User::findByUsername($username);
        if (!$this->_user) {
            throw new InvalidArgumentException('No se encontró el usuario.');
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'repeatpassword'], 'required', 'message'=>'Inserte la contraseña'],
            ['password', 'string', 'min' => 6],
            //[['password'], 'compare', 'compareAttribute' => 'repeatpassword'],
            ['repeatpassword', 'validatepass'],
        ];
    }

    public function validatepass($attribute, $params, $validator) {
        if($this->$attribute !== $this->password) {
            $validator->addError($this, $attribute, 'Las contraseñas no coinciden');
            return false;
        }
        else
            return true;
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        //$user->removePasswordResetToken();

        return $user->save(false);
    }
}
