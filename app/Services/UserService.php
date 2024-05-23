<?php

namespace App\Services;

use App\Exceptions\FailedAction;
use Illuminate\Http\Response;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(array $data)
    {
        try {
            $user = $this->userRepository->create($data);
            return $user;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to create user. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $user = $this->userRepository->update($id, $data);
            return $user;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to update user. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $user = $this->userRepository->delete($id);
            return $user;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to delete user. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function showProfileData(int $id)
    {
        try {
            $user = $this->userRepository->find($id);

            if (!$user) {
                return 'Usuário não encontrado';
            }

            $text = "Veja abaixo seus dados cadastrados:\n\n";
            $text .= "*Nome:* {$user->name}\n";
            $text .= "*Email:* {$user->email}\n";
            if ($user->phone) {
                //format phone number in brasilian format
                $phone = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $user->phone);
                $text .= "*Telefone:* {$phone}\n";
            }
            if ($user->dob) {
                $dob = Carbon::parse($user->dob)->format('d/m/Y');
                $text .= "*Data de nascimento:* {$dob}\n";
            }
            if ($user->height) {
                //altura em metros
                $height = $user->height / 100;
                $text .= "*Altura:* {$height} m\n";
            }
            if ($user->weight) {
                $text .= "*Peso:* {$user->weight} kg\n";
            }
            if ($user->gender) {
                $gender = strtolower($user->gender) === 'm' ? 'Masculino' : 'feminino';
                $text .= "*Sexo:* {$gender}\n";
            }
            if ($user->daily_water_amount) {
                $text .= "*Quantidade diária de água:* {$user->daily_water_amount} ml\n";
            }
            if ($user->activity_level) {
                $activity_levels = [
                    '0.2' => 'Sedentário',
                    '0.375' => 'Pouco ativo (1 a 3 vezes na semana)',
                    '0.55' => 'Ativo (3 a 5 vezes na semana)',
                    '0.725' => 'Muito ativo (Todos os dias)',
                    '0.9' => 'Extremamente ativo (Atleta profiissional)'
                ];
                $level = $activity_levels[$user->activity_level];
                $text .= "*Nível de atividade física:* {$level}\n";
            }

            return $text;
        } catch (\Exception $e) {
            return 'Falha ao exibir dados do perfil';
            Log::error('Failed to show profile data. Error: ' . $e->getMessage());
        }
    }

    protected function updateProfileData(int $id, array $data)
    {
        try {
            $user = $this->userRepository->update($id, $data);
            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to update profile data. Error: ' . $e->getMessage());
            throw new FailedAction('Falha ao atualizar dados do perfil. Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateProfileName($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['name' => $data['name']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile name. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfileEmail($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['email' => $data['email']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile email. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfilePassword($data)
    {
        try {
            $user = $this->userRepository->find($data['user_id']);
            if (!Hash::check($data['old_password'], $user->password)) {
                return false;
            }
            if ($data['new_password'] !== $data['confirm_password']) {
                return false;
            }
            $this->updateProfileData($data['user_id'], ['password' => Hash::make($data['new_password'])]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile password. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfilePhone($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['phone' => $data['phone']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile phone. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfileDob($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['dob' => $data['dob']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile dob. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfileHeight($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['height' => $data['height']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile height. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfileWeight($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['weight' => $data['weight']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile weight. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfileGender($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['dob' => $data['dob']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update birthday. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfileDailyWaterAmount($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['daily_water_amount' => $data['daily_water_amount']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update daily water amount. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfilePhysicalActivityLevel($data)
    {
        try {
            $niveis = [
                1 => 0.2,
                2 => 0.375,
                3 => 0.55,
                4 => 0.725,
                5 => 0.9
            ];
            $data['activity_level'] = $niveis[(int)$data['activity_level']];
            $this->updateProfileData($data['user_id'], ['activity_level' => $data['activity_level']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update activity level. Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfilePhoto($data)
    {
        try {
            $this->updateProfileData($data['user_id'], ['image' => $data['photo']]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update profile photo. Error: ' . $e->getMessage());
            return false;
        }
    }
}
