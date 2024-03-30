<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\AppointmentRepository;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'App:CheckAppointments',
    description: 'Checks if there will be an appointment in 24h, if it does, send an email to the user linked to the appointment.',
)]
class AppCheckAppointmentsCommand extends Command
{
    private $appointmentRepository;
    private $mailer;
    private $logFilePath;

    public function __construct(AppointmentRepository $appointmentRepository, MailerInterface $mailer, KernelInterface $kernel)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->mailer = $mailer;
        $this->logFilePath = $kernel->getLogDir() . '/check_appointments.log';

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $appointments = $this->appointmentRepository->findAppointmentsInNext24Hours();

        if (count($appointments) > 0) {
            $this->logMessage("Command started at " 
                . (new \DateTime())->format('Y-m-d H:i:s') 
                . " found " . count($appointments) 
                . " first appointment is at "
               . $appointments[0]->getStartDate()->format("H:i")
            );
        } else {
            $this->logMessage("Command started at " . (new \DateTime())->format('Y-m-d H:i:s') . " found no appointments.");
        }

        try {
            foreach ($appointments as $appointment) {

                $user = $appointment->getUser();
                $startDateTime = $appointment->getStartDate()->format("H:i");

                $email = (new Email())
                    ->from('emailerdemo8@gmail.com')
                    ->to($user->getEmail())
                    ->subject("Reminder for your appointment tomorrow at " . $startDateTime)
                    ->text("You have an appointment tomorrow at " . $startDateTime . " within the next 24 hours ");

                $this->mailer->send($email);
                $this->logMessage('Email sent to: ' . $user->getEmail());
            }
        } catch(Exception $e) {
            $this->logMessage('Exception: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }

    private function logMessage(string $message): void
    {
        $timestampedMessage = sprintf("[%s] %s\n", (new \DateTime())->format('Y-m-d H:i:s'), $message);
        file_put_contents($this->logFilePath, $timestampedMessage, FILE_APPEND);
    }
}
