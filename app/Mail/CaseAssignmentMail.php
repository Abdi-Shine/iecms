<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CaseAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $case;
    public $employee;
    public $role;

    public function __construct($case, $employee, $role)
    {
        $this->case = $case;
        $this->employee = $employee;
        $this->role = $role;
    }

    public function build()
    {
        return $this->subject('Waxaa lagugu qoray gal-dacwadeed cusub.')
                    ->html($this->getEmailBody());
    }

    protected function getEmailBody()
    {
        $fileNo = $this->case->FileNo;
        $openDate = \Carbon\Carbon::parse($this->case->OpenDate)->format('d/m/Y');
        $empName = $this->employee->EmpName;
        $role = $this->role;

        return "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <p>Mudane/Marwo, <strong>{$empName}</strong>,</p>
                <p>Waxaan kugu wargelinaynaa in lagugu daray gal-dacwadeed leh sumadda <strong>{$fileNo}</strong>, kaas oo la furay taariikhdu markay ahayd <strong>{$openDate}</strong>. Doorkaaga gal-dacwadeedkan waa <strong>{$role}</strong>.</p>
                <p>Fadlan booqo Nidaamka Maamulka Dacwadaha ee Maxkamadda (IECMS) si aad u aragto faahfaahinta dacwadda una qabato howlaha laguu igmaday sida ugu habboon.</p>
                <p>Mahadsanid,</p>
                <hr>
                <p style='font-size: 0.8em; color: #777;'>Tani waa fariin si toos ah uga timid nidaamka IECMS. Fadlan ha ka soo jawaabin.</p>
            </div>
        ";
    }
}
