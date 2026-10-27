# Lead Generation - AI Voice Agent for Parents

Admin panel > **Lead Generation** (sidebar). Visible to `super_admin` and `admin`.

## Install (2 commands)
```
php artisan migrate
php artisan optimize:clear
```
Then open **Lead Generation > Parent Contacts** and add (or CSV-import) each parent's phone number
against the student. This table is the "Students sheet" the agent checks phone numbers against.

## What it does
| Parents can ask | Source |
|---|---|
| Attendance: overall %, last 30 days %, recent absent dates, current absence streak | `attendances` |
| Test marks: per-subject average, last score, change from previous test, improving / declining / stable | `marks` + `exams` |
| Weakest and strongest subject | average of all tests per subject |
| Fees: total, paid, due, due date | `fees` |
| Last 5 teacher remarks | `marks.remarks` + `attendances.remarks` |
| Full report: good news -> needs attention -> one suggestion | all of the above |

Replies follow the parent's language: Hindi (Devanagari), Hinglish (Roman) or English.
Every number is calculated in PHP from your database. The AI (optional) only words the answer.

## Automatic handling
* Phone number checked against Parent Contacts; unknown numbers get no data.
* 3 failed attempts -> number blocked for 60 minutes (change in Agent Settings; unblock from Query Log > Blocked Numbers).
* Unknown student ID typed -> alert to the owner (panel notification + WhatsApp/SMS if configured).
* Complaint / refund / legal messages -> sent to the owner, the AI never answers them (fixed acknowledgement only).
* Parent with two children -> "Which child?" (reply with number, name or "both").
* Voice note or image on WhatsApp -> asks the parent to write text.
* Every question and reply is logged (Query Log, CSV export).
* AI or database failure -> polite "system issue" message, error logged, owner alerted (max once per 15 min).

## Calling parents with your script
1. **Call Scripts**: write your script (Hindi / Hinglish / English) with placeholders such as
   `{parent_name} {student_name} {due_amount} {due_date} {attendance_percent}`. "Add sample scripts" gives 4 examples.
2. **Voice Console > Rehearse an outbound call script**: hear it spoken in your browser first.
3. **Outbound Calls**: pick a script and who to call (chosen students / everyone with fees due / absent 2+ days).
   The agent reads the script, then asks "Do you have any question?" and answers using the same rules as above.
   The conversation is saved as a transcript. Calls only go out inside the call hours (default 09:00-19:00).

## Channels
* **Voice Console** (works immediately): type or speak (browser microphone), replies are spoken aloud.
* **WhatsApp + phone calls** (Twilio): put the keys in `.env`, then in the Twilio console set
  * WhatsApp "when a message comes in"  -> `https://YOUR-DOMAIN/webhooks/lead/whatsapp`
  * Phone number "a call comes in"      -> `https://YOUR-DOMAIN/webhooks/lead/voice/incoming`
  * `APP_URL` in `.env` must be that public https domain (use ngrok while testing on localhost).
  All webhooks verify the Twilio signature.

## Before you go live (India)
* Outbound calls to parents must respect TRAI DND/DLT rules - use your registered number / provider and only call parents
  who gave consent. Tell parents the call is from an automated assistant.
* Twilio India calling needs a verified number. Another provider (Exotel, Knowlarity, ...) can be used by adapting
  `app/Services/LeadAgent/TwilioClient.php` and `LeadWebhookController.php` - the agent brain (`AgentService`) is provider-independent.

## Code map
```
config/leadagent.php
database/migrations/2026_10_27_100000_create_lead_generation_tables.php
app/Services/LeadAgent/        AgentService (brain), StudentDataService (numbers), ReplyBuilder (3 languages),
                               IntentDetector, LanguageDetector, LlmClient, TwilioClient, OwnerNotifier, ScriptRenderer
app/Models/Lead/               guardians, query logs, attempts, alerts, sessions, scripts, calls
app/Http/Controllers/Admin/LeadGeneration/   admin pages
app/Http/Controllers/Public/LeadWebhookController.php   WhatsApp + voice webhooks
resources/views/lead-generation/              admin views
tests/Feature/LeadAgentTest.php               18 tests
```
