# Lead Generation — AI agent (WhatsApp + calls)

Admin panel → **Lead Generation** folder: Overview, Leads, Import Leads, Scripts, Test AI Agent, AI Settings.

## 1. Install (5 minutes)
```bash
php artisan migrate            # creates leads, lead_messages, lead_scripts + "manage leads" permission
php artisan config:clear
php artisan queue:work         # REQUIRED: AI replies, campaigns and calls run on the queue (QUEUE_CONNECTION=database)
```
Keep `queue:work` running (Supervisor in production). The scheduler (`php artisan schedule:run` every minute via cron) is only needed for automatic follow-ups.

Add the keys from `.env.example` (bottom section) to your `.env`.

## 2. Test the AI first (needs only the Claude key)
1. `ANTHROPIC_API_KEY=...` in `.env`, then `php artisan config:clear`.
2. Lead Generation → **AI Settings** → fill "School knowledge" (fees, admission steps, timings, FAQs).
3. Lead Generation → **Test AI Agent**. Try a stranger, then a registered parent's/student's phone number to test attendance / marks / fees answers.

## 3. WhatsApp (Meta Cloud API)
1. developers.facebook.com → create app → add WhatsApp → note **Phone number ID**, create a permanent **System User token** → `WHATSAPP_TOKEN`, `WHATSAPP_PHONE_NUMBER_ID`.
2. App settings → Basic → **App Secret** → `WHATSAPP_APP_SECRET`.
3. Choose any random string → `WHATSAPP_VERIFY_TOKEN`.
4. WhatsApp → Configuration → Callback URL = `https://YOUR-DOMAIN/webhooks/whatsapp`, Verify token = the string above, subscribe to **messages**.
5. Create a message template (category Marketing/Utility) with two variables, e.g.
   `Hello {{1}}, thank you for your interest in {{2}}. How can we help you with admission?`
   After approval → `WHATSAPP_TEMPLATE_NAME` / `WHATSAPP_TEMPLATE_LANG`.
   (WhatsApp only allows free text within 24 h of the person's last message; a template starts conversations.)

## 4. Phone calls (Vapi)
1. vapi.ai → create account, import/buy a phone number → `VAPI_PHONE_NUMBER_ID`, API key → `VAPI_API_KEY`.
2. Create an **Assistant** (choose voice, language/transcriber, model). Its system prompt must contain these variables:
   ```
   You are {{agent_name}} from {{school_name}}, calling {{lead_name}} about admission for {{student_name}} ({{class_interest}}).
   Follow this script, but answer questions naturally. Be brief and polite. If they ask to stop, apologise and end the call.
   SCRIPT: {{script}}
   SCHOOL FACTS (only use these): {{knowledge}}
   ```
   → `VAPI_ASSISTANT_ID`.
3. Assistant/Org **Server URL** = `https://YOUR-DOMAIN/webhooks/voice`, secret = `VAPI_WEBHOOK_SECRET` (sent as `x-vapi-secret`), enable the *end-of-call-report* message.
4. India: check the provider's rules for outbound calling numbers / DLT before calling at scale.

## 5. Daily use
- **Import**: Import Leads (Excel/CSV) or "Sync admission enquiries". New website admission forms become leads automatically.
- **Scripts**: write call scripts and WhatsApp opening messages (`{name} {student_name} {class} {school}`).
- **Campaign**: Leads → tick rows → *Send WhatsApp* or *AI Call*.
- **Inbound**: anyone who messages your WhatsApp number gets an AI reply; unknown numbers become leads.
- **Handoff**: if the AI can't help it flags "Needs a human" (sidebar badge + notification). Replying yourself pauses the AI for that lead.
- Leads who reply STOP are never contacted again.

## Privacy rules built in
- Attendance / marks / fees are only shared when the sender's number matches a student's own number or a parent's father/mother phone linked to the student. The AI cannot choose a student itself.
- Contacting requires a consent flag (import form asks you to confirm).

## Known limits
- Parent ↔ student matching uses the `parent_student` table, which is not created by your current migrations. Until parents are linked there, only the **student's own phone number** (users.phone) is verified.
- During a live call the voice agent uses the prompt/knowledge you give it; it does not query your database mid-call (WhatsApp does).
