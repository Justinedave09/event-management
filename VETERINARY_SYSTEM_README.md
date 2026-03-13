# Veterinary Appointment System

This system has been converted from an event management system to a veterinary clinic appointment booking system with automated email notifications.

## Key Changes Made

### 1. Database Changes
- Database renamed from `db_event_management` to `db_vet_appointment`
- Table `tbl_reservations` renamed to `tbl_appointments`
- Added new fields for pet information:
  - `pet_name` - Name of the pet
  - `pet_type` - Type of pet (Dog, Cat, Bird, etc.)
  - `pet_breed` - Breed of the pet
  - `appointment_type` - Type of appointment (General Checkup, Vaccination, Surgery, etc.)
- Removed `ucount` field (no longer needed)

### 2. Email Notification System
The system now sends automated emails to clients at three stages:

#### a) Appointment Booking Confirmation
When a client books an appointment, they receive an email with:
- Pet details (name, type)
- Appointment date and time
- Appointment type
- Status: Pending confirmation

#### b) Appointment Approved
When staff approves the appointment:
- Confirmation email sent to client
- Reminder to arrive 10 minutes early
- Contact information for rescheduling

#### c) Appointment Denied
If appointment is declined:
- Notification email sent
- Reason for denial
- Instructions to contact clinic for alternative times

### 3. System Configuration
Updated in `library/config.php`:
- Site title: "Veterinary Appointment System"
- Email sender: appointments@vetclinic.com

### 4. Booking Process Updates
- Modified `api/process.php` to handle pet information
- Calendar view now shows: Owner name - Pet name (Appointment type)
- Email notifications integrated into booking and approval workflows

## Setup Instructions

### 1. Database Setup

1. Import the updated SQL file: `db-script/db_vet_appointment.sql`
2. The database will be created as `db_vet_appointment`
3. Default admin credentials:
   - Username: admin
   - Password: admin

### 2. Email Configuration
To enable email notifications, configure your PHP mail settings:

**For Development (Windows with XAMPP/WAMP):**
1. Edit `php.ini`:
```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = your-email@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
```

2. Edit `sendmail.ini` (in sendmail folder):
```ini
smtp_server=smtp.gmail.com
smtp_port=587
auth_username=your-email@gmail.com
auth_password=your-app-password
force_sender=your-email@gmail.com
```

**For Production:**
- Configure your server's mail settings
- Update the sender email in `library/mail.php` (currently: appointments@vetclinic.com)
- Consider using PHPMailer or similar library for better email delivery

### 3. Update Email Address
In `library/mail.php`, change the sender email:
```php
$header = "From:your-actual-email@yourdomain.com \r\n";
```

## How It Works

### Client Booking Flow
1. Client fills out appointment form with:
   - Personal information (name, address, phone, email)
   - Pet information (name, type, breed)
   - Appointment date and time
   - Appointment type

2. System checks if clinic is closed on selected date

3. Appointment is created with "PENDING" status

4. Client receives confirmation email immediately

### Staff Approval Flow
1. Staff views pending appointments in dashboard

2. Staff can approve or deny appointments

3. When status is changed:
   - Database is updated
   - Automated email is sent to client with new status
   - Client receives either confirmation or denial notification

### Calendar Display
- Green: Approved appointments
- Orange: Pending appointments
- Red: Denied appointments
- Gray: Clinic closed days

## Required Form Fields

When creating the booking form, ensure these fields are included:

```html
<input type="text" name="name" required>
<input type="text" name="address" required>
<input type="text" name="phone" required>
<input type="email" name="email" required>
<input type="date" name="rdate" required>
<input type="time" name="rtime" required>
<input type="text" name="pet_name" required>
<select name="pet_type" required>
  <option value="Dog">Dog</option>
  <option value="Cat">Cat</option>
  <option value="Bird">Bird</option>
  <option value="Rabbit">Rabbit</option>
  <option value="Other">Other</option>
</select>
<input type="text" name="pet_breed">
<select name="appointment_type">
  <option value="General Checkup">General Checkup</option>
  <option value="Vaccination">Vaccination</option>
  <option value="Surgery">Surgery</option>
  <option value="Emergency">Emergency</option>
  <option value="Follow-up">Follow-up</option>
</select>
```

## Testing Email Functionality

1. Book a test appointment
2. Check the email inbox of the address provided
3. Approve/deny the appointment from admin panel
4. Verify second email is received

## Troubleshooting

### Emails Not Sending
- Check PHP mail configuration
- Verify SMTP settings
- Check spam/junk folder
- Enable error logging in PHP
- Test with a simple mail() function first

### Database Connection Issues
- Verify database name is `db_vet_appointment`
- Check credentials in `library/config.php`
- Ensure MySQL service is running

## Future Enhancements

Consider adding:
- SMS notifications
- Appointment reminders (24 hours before)
- Online payment integration
- Medical history tracking
- Prescription management
- Multiple veterinarian scheduling

## Support

For issues or questions, refer to the original documentation or contact your system administrator.
