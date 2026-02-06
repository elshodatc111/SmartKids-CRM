<?php
namespace App\Jobs;
use App\Services\EskizSmsService;
use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public string $phone,public string $message) {}
    public function handle(EskizSmsService $eskiz): void{
        $log = SmsLog::create([
            'phone'   => $this->phone,
            'message'=> $this->message,
            'status' => 'pending',
        ]);
        try {
            $response = $eskiz->sendSms($this->phone, $this->message);
            $log->update([
                'status' => 'sent',
                'provider_message_id' => $response['id'] ?? null,
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()],
            ]);
            Log::error('SMS job failed', [
                'phone' => $this->phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
