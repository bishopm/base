<?php

namespace Bishopm\Connexion\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class NewUserRegistration extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->notification_channel=="Slack"){
            return ['slack'];
        } else {
            return ['mail'];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line("*Hi " . $notifiable->individual->firstname . "!* \n Your user profile has been updated!");
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->to($notifiable->slack_username)
            ->content("*Hi " . $notifiable->individual->firstname . "!* \n Your user profile has been updated!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
