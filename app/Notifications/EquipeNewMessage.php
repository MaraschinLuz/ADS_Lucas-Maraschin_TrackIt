<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EquipeNewMessage extends Notification
{
    use Queueable;

    protected $equipeId;
    protected $userName;
    protected $mensagem;

    public function __construct(int $equipeId, string $userName, string $mensagem)
    {
        $this->equipeId = $equipeId;
        $this->userName = $userName;
        $this->mensagem = $mensagem;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'equipe_message',
            'equipe_id' => $this->equipeId,
            'user' => $this->userName,
            'mensagem' => $this->mensagem,
            'message' => "Nova mensagem de {$this->userName}: {$this->mensagem}",
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('equipe.chat', [], false));
        return (new MailMessage)
            ->subject('Nova mensagem no chat da equipe')
            ->greeting('Olá,')
            ->line("{$this->userName} enviou uma nova mensagem na sua equipe.")
            ->line('Mensagem: ' . $this->mensagem)
            ->action('Abrir chat da equipe', $url)
            ->line('Esta é uma notificação automática.');
    }
}
