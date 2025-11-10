<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ChamadoStatusChanged extends Notification
{
    use Queueable;

    protected $chamadoId;
    protected $titulo;
    protected $old;
    protected $new;
    protected $by;

    public function __construct(int $chamadoId, string $titulo, ?string $old, string $new, string $by)
    {
        $this->chamadoId = $chamadoId;
        $this->titulo = $titulo;
        $this->old = $old;
        $this->new = $new;
        $this->by = $by;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'chamado_status',
            'chamado_id' => $this->chamadoId,
            'titulo' => $this->titulo,
            'old' => $this->old,
            'new' => $this->new,
            'by'  => $this->by,
            'message' => "Status do chamado '{$this->titulo}' alterado de '{$this->old}' para '{$this->new}' por {$this->by}.",
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('chamados.show', ['chamado' => $this->chamadoId], false));
        return (new MailMessage)
            ->subject('Status do chamado atualizado')
            ->greeting('Olá,')
            ->line("O status do chamado '{$this->titulo}' foi alterado.")
            ->line("De: " . ($this->old ?: '—') . " → Para: " . $this->new)
            ->line('Por: ' . $this->by)
            ->action('Ver chamado', $url)
            ->line('Esta é uma notificação automática.');
    }
}
