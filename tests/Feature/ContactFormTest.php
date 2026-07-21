<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    private array $validPayload = [
        'name' => 'Budi',
        'email' => 'budi@example.com',
        'subject' => 'Peluang kerja sama',
        'message' => 'Halo David, saya ingin mendiskusikan peluang kerja sama untuk proyek IT.',
        'website' => '',
    ];

    public function test_contact_form_rejects_invalid_data(): void
    {
        $response = $this->from('/')->post('/contact', [
            'name' => '',
            'email' => 'bukan-email',
            'subject' => '',
            'message' => 'Terlalu pendek',
        ]);

        $response
            ->assertRedirect('/')
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_disabled_contact_form_does_not_send_mail(): void
    {
        Mail::fake();
        config()->set('portfolio.contact.enabled', false);

        $this->from('/')
            ->post('/contact', $this->validPayload)
            ->assertRedirect('/')
            ->assertSessionHasErrors('contact');

        Mail::assertNothingSent();
    }

    public function test_enabled_contact_form_sends_mail(): void
    {
        Mail::fake();
        config()->set('portfolio.contact.enabled', true);
        config()->set('portfolio.contact.email', 'david@example.com');

        $this->from('/')
            ->post('/contact', $this->validPayload)
            ->assertRedirect('/')
            ->assertSessionHas('contact_success');

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail): bool {
            return $mail->hasTo('david@example.com')
                && $mail->messageData['email'] === 'budi@example.com';
        });
    }

    public function test_contact_route_is_rate_limited(): void
    {
        Mail::fake();
        config()->set('portfolio.contact.enabled', true);
        config()->set('portfolio.contact.email', 'david@example.com');

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post('/contact', $this->validPayload)->assertRedirect('/');
        }

        $this->post('/contact', $this->validPayload)->assertStatus(429);
    }
}
