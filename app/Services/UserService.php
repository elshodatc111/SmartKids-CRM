<?php
namespace App\Services;

use App\Models\User;
use App\Models\FinanceHistory;
use App\Models\Finance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserService{

    public function getAllEmployeesExceptAuth(): Collection{
        // Bu yerda kelajakda SMS yuborish yoki boshqa amallarni qo'shish mumkin
        // $this->sendUpdateNotification($user);
        return User::where('id', '!=', auth()->id())->select(['id', 'name', 'phone', 'salary_amount','birth', 'series', 'image', 'type','is_active', 'created_at'])->latest()->get();
    }

    public function store(array $data): User{
        // Bu yerda kelajakda SMS yuborish yoki boshqa amallarni qo'shish mumkin
        // $this->sendUpdateNotification($user);
        return User::create([
            'name'          => $data['name'],
            'phone'         => $data['phone'],
            'salary_amount' => $data['salary_amount'],
            'birth'         => $data['birth'],
            'series'        => $data['series'] ?? null,
            'type'          => $data['type'],
            'is_active'     => true,        
            'password'      => Hash::make('password'),
            'image'         => null,
        ]);
    }

    public function updateEmployee(array $data, int $id): User{
        $user = User::findOrFail($id);
        $user->update($data);
        // Bu yerda kelajakda SMS yuborish yoki boshqa amallarni qo'shish mumkin
        // $this->sendUpdateNotification($user);
        return $user;
    }

    public function paySalary(array $data, int $employeeId){
        return DB::transaction(function () use ($data, $employeeId) {
            $finance = Finance::lockForUpdate()->first();
            if (!$finance) {
                throw new \Exception("Moliya jadvali (finances) ma'lumotlari topilmadi.");
            }
            $paymentType = $data['type']; // 'cash', 'card' yoki 'bank'
            $amount = $data['amount'];
            if ($finance->$paymentType < $amount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => ["Mablag' yetarli emas! Kassada jami " . $finance->$paymentType . " so'm mavjud."]
                ]);
            }
            $finance->$paymentType -= $amount;
            $finance->save();
            $payment = DB::table('finance_histories')->insert([
                'user_id'     => $employeeId,
                'amount'      => $amount,
                'type'        => $paymentType,
                'description' => $data['description'],
                'reason'      => $data['reason'],
                'admin_id'    => auth()->id(), 
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            return [
                'finance' => $finance,
                'status'  => $payment
            ];
        });
    }


}