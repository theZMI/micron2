<h1 class="my-4">Выделение программного кода и Маркеров</h1>
<div>
    [code=C++]
    int main()
    {
        return 0;
    }
    [/code]

    <br>

    [code=Php]
    class UserPwdRecoverModel extends \Models\ModelExtends
    {
        public function __construct($id = null)
        {
            parent::__construct('user_pwd_recover', $id);
        }

        public function generate($user_id)
        {
            $this->user_id = $user_id;
            $this->code = strval(mt_rand(10000, 99999));
            $this->ttl = time() + Config(['user_model', 'pwd_recover_time']);
            $this->create_time = time();
            return $this->flush();
        }

        public function isValidCode($userCode, $userEmail)
        {
            $user = new UserModel((new UserModel())->getIdByEmail($userEmail));
            $code_id = $this->db->selectCell("SELECT `id` FROM ?# WHERE `user_id` = ? ORDER BY `create_time` DESC LIMIT 1", $this->table, $user->id);
            $model = new self($code_id);
            // TODO: Ввести проверку ttl
            return $model->isExists() && $model->code === $userCode;
        }
    }
    [/code]
</div>