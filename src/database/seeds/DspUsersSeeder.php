<?php

use App\Checkpoint;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DspUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function run()
    {
        $users = json_decode(Storage::disk('dsp_data')->get('users_dsp.json'), true);

//        use this only when the checkpoints list is scrambled again
//        $users = $this->generateUsers(Checkpoint::all(Checkpoint::API_BORDER_URL(), ['status' => 'active']));

        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();

        $this->call('AdminUserSeeder');

        foreach ($users as $key => $user) {
            DB::table('users')->insert([
                'username' => $user['username'],
                'name' => $user['username'],
                'password' => Hash::make($user['password']),
                'checkpoint' => $user['checkpoint-id'],
                'generic_password' => $user['password'],
                'import_order_number' => $key
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }

    /**
     * Generate users based on active checkpoints
     *
     * @param array $activeBorderPoints
     *
     * @return array
     */
    private function generateUsers(array $activeBorderPoints) :array {
        $users = [];
        $searchArr = ['ă', 'â', 'î', 'ș', 'ț', ' ', ',', 'aeroportul', '-international', 'p.t.f.-', '(feroviar)', '(rutier)', 'aeroport'];
        $replaceArr = ['a', 'a', 'i', 's', 't', '-', '', 'aero', '', '', 'ferov', 'rutier', 'aero'];
        $passwords = $this->generateUniquePass(count($activeBorderPoints) * 3,8, []);
        $counter = 1;
        foreach ($activeBorderPoints as $border) {
            $shortName = str_replace(['-i', '-ii'], ['-1', '-2'],
                str_replace($searchArr, $replaceArr, strtolower(trim($border['name']))));
            for ($i = 1; $i <= 3; $i++) {
                $users[$counter]['id'] = $counter;
                $users[$counter]['username'] = $shortName . '-' . $i;
                $users[$counter]['password'] = reset($passwords);
                array_shift($passwords);
                $users[$counter]['checkpoint-name'] = $border['name'];
                $users[$counter]['checkpoint-id'] = $border['id'];
                $counter++;
            }

        }

        return $users;
    }

    /**
     * @param int   $number
     * @param int   $length
     * @param array $destination
     *
     * @return array
     */
    private function generateUniquePass(int $number, int $length, array $destination) : array {
        for($i = 1; $i <= $number; $i++) {
            if (!in_array($temp = substr(str_shuffle('23456789abcdefghkmnprstuvxz'),1,$length), $destination)) {
                $destination[$i] = $temp;
            }
        }

        return $destination;
    }
}
