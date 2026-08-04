<?php

namespace App\Modules\Contacts\Tests\Feature;

use App\Modules\Contacts\Http\Requests\StoreContactRequest;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Shared\Support\ModuleRegistry;
use Tests\TestCase;

class ContactsModuleTest extends TestCase
{
    public function test_it_loads_the_module_descriptor(): void
    {
        $this->assertNotNull(app(ModuleRegistry::class)->find('contacts'));
    }

    public function test_contact_request_fields_are_fillable(): void
    {
        $fillable = (new Contact)->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('phone', $fillable);
        $this->assertContains('subject', $fillable);
        $this->assertContains('message', $fillable);
        $this->assertContains('terms_accepted', $fillable);
        $this->assertContains('read_at', $fillable);
    }

    public function test_contact_submission_validation_matches_frontend_form(): void
    {
        $rules = (new StoreContactRequest)->rules();

        $this->assertSame('required|string|max:255', $rules['name']);
        $this->assertSame('required|email|max:255', $rules['email']);
        $this->assertSame('nullable|string|max:50', $rules['phone']);
        $this->assertSame('nullable|string|max:255', $rules['subject']);
        $this->assertSame('required|string|max:5000', $rules['message']);
        $this->assertSame('accepted', $rules['terms_accepted']);
    }
}
