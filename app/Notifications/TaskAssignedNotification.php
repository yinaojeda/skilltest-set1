<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to users when they are assigned a new task.
 */
class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Assigned: ' . $this->task->title)
            ->greeting('Hi ' . $notifiable->name . ' 👋')
            ->line('You have been assigned a new task.')
            ->line('Title: ' . $this->task->title)
            ->line('Status: ' . $this->task->status)
            ->line('Due date: ' . ($this->task->due_date ?? '—'))
            ->line('Project ID: ' . $this->task->project_id)
            ->line('Task ID: ' . $this->task->id)
            ->salutation('— SkillTest Bot');
    }
}
