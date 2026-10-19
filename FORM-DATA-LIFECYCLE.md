# V-02: form consumers and data lifecycle

Status: **local verification complete**, within the local-only scope confirmed by
the user. This is not closure of the full production/operational V-02 acceptance.

Verified 2026-10-19. Scope: local source, read-only WordPress configuration, and
public GET requests. No form POSTs, test submissions, exports, emails, uploads or
submission records were created. No personal submission contents were inspected.

## Local consumers

| Page | Rendered consumer | Submitted fields |
| --- | --- | --- |
| `/contact/` | CF7 form 937 | Name, email, topic, topic categories, message, privacy acceptance |
| `/newsletter/` | CF7 form 938 | Email, newsletter consent |
| `/jobs/` | CF7 form 584 | Position, name, email, phone, motivation, resume, application document, consent |

All three forms were found in rendered local HTML, with a privacy-policy link.
All three definitions have mail active, a recipient configured, and skip_mail
disabled. This establishes a configured journey, not successful email delivery.
Recipients and uploaded/submitted personal data are intentionally not reproduced.

Other supported shortcode slots exist in how-to-apply, news, news-grid and
procurement-resources. The local published content inspected had no CF7 shortcode
configured in those slots. Recheck configuration when content changes.

## Endpoint and storage map

### Custom newsletter/document submission

- POST `/wp-admin/admin-ajax.php`, action `mca_submit_contact_form`, for logged-in
  and anonymous users. Handler: `inc/actions.php`, `mca_submit_contact_form()`.
- Accepts `full_name`, `email`, `industry`, `privacy`, `security`, optional
  `pdf_url`. Limits: name/industry 255 characters, email 254, PDF URL 2048.
- Calls `MCA\FormSubmissions\insert_submission()` and stores ID, name, email,
  industry and server timestamp in `{prefix}mca_form_submissions`.
- Privacy acceptance is checked but is not stored with its timestamp or notice
  version. PDF URL is returned to the caller; it is not stored in the row.
- The handler does not send email or store application files.
- No first-party caller or nonce generator for this action was found in the
  current theme sources. Current local forms use CF7 instead. Do not remove this
  endpoint solely on that basis: production still declares a consumer below.

### Submission list and CSV

- Admin page `admin.php?page=mca-form-submissions` reads the custom table.
- Export: `admin-post.php?action=mca_export_form_submissions`, registered for
  authenticated users; requires `mca_manage_form_submissions` and an export nonce.
- Exports name, email, industry and creation time. Formula-leading text is
  neutralized at CSV output, leaving stored text unchanged.
- Administrator access is bootstrapped once; other operators need an explicit
  capability grant. Operator approval and downloaded CSV custody remain owner
  responsibilities, not an application-enforced retention policy.
- Evidence: `inc/form-submissions.php`, `render_admin_page()`, `export_csv()`,
  `csv_text()`; `inc/admin/class-form-submissions-list-table.php`.

### Application and other CF7 forms

- CF7's normal AJAX journey posts to
  `/wp-json/contact-form-7/v1/contact-forms/{id}/feedback` (REST query-string form
  may be used depending on permalink configuration).
- There is no separately registered custom application submission endpoint in
  this theme. Application is not universally preview-only: configured shortcodes
  render live CF7 forms. Without a shortcode, the fallback preview has a disabled
  submit button and JavaScript prevents submission.
- Evidence: `template-parts/blocks/application/application.php`,
  `inc/fields/application.php`, `src/js/__modules/application.js`.
- Local CF7 sends via `wp_mail()`; its submission destructor removes temporary
  uploaded files. This does not delete copies in recipient mailboxes or backups.
  Evidence: installed CF7 `includes/mail.php` and `includes/submission.php`.
- No Flamingo/archive plugin is active locally. CF7 forms are not wired to the
  theme's custom submission table/CSV. Other server/plugin hooks or production
  integrations cannot be excluded without their configuration.
- A local-only SMTP override exists in `inc/actions.php`; transport and delivery
  were deliberately not exercised during this verification.

## Abuse controls and their scope

- Custom handler: POST requirement, public nonce, scalar/length checks, required
  fields/privacy, WordPress disallowed-key filtering, database counters of 10
  attempts per peer IP and 3 per normalized email per 15 minutes. Storage failure
  rejects attempts. A public nonce is not authentication or a spam challenge.
- Counter keys use salted hashes in `{prefix}mca_submission_limits`. Expiry is
  enforced/old entries deleted on a later counter request, not by a deletion cron.
  The code uses REMOTE_ADDR; trusted-proxy configuration must be confirmed by host.
- These custom counters do not apply to CF7's REST feedback endpoint.
- Local CF7 Turnstile and reCAPTCHA services report unconfigured. No claim is made
  that local CF7 has equivalent rate limiting or verified challenge enforcement.
- Production pages load Turnstile scripts, and both public environments have
  previously returned Cloudflare headers. Neither fact proves WAF rules,
  server-side token enforcement or coverage of every form endpoint.
- No load/rate/challenge tests were performed because they could create records.

## Environment evidence from public GETs

| Environment / page | Observation |
| --- | --- |
| Production `https://www.mcakosovo.org/contact/` | CF7 form 7, name/email/subject/message/privacy; Turnstile script present |
| Production `/job-application/` | CF7 form 205 with name/email/phone, two file fields, message/position/privacy; configured live form, delivery untested |
| Production `/newsletter/` | Form declares `data-action="mca_submit_contact_form"` and production admin-ajax URL; name/email/industry/privacy; submit button initially disabled |
| Staging `https://clleancode.net/mca-ks/contact/`, `/newsletter/`, `/jobs/` | No CF7 or application-preview form found in inspected HTML; only site search detected |
| Staging `/job-application/` | HTTP 404 |

Production newsletter markup proves a declared consumer, not successful client
execution or server-side protections matching local code. Staging and production
form/plugin configuration are not established by local configuration. Do not
copy local CF7 IDs to staging blindly: IDs must resolve in that environment.

The user subsequently confirmed that Contact Form 7 was deliberately deactivated
on staging. This explains the missing CF7 forms without establishing a theme
defect. Recheck rendered forms after reactivation; staging reactivation and
delivery testing are outside the completed local-only verification.

## Retention, deletion and privacy ownership

- No scheduled deletion, retention interval, custom deletion UI or WordPress
  personal-data eraser integration was found for the submission table. Rows have
  no automatic expiry in the theme.
- Exported CSVs, recipient email/attachments and backups need separate deletion
  handling. Local backup plugins include UpdraftPlus and Duplicator; their
  schedules, off-site copies and retention policies were not inspected.
- Production `/privacy-policy/` describes purpose/legal-need based retention and
  eventual deletion/anonymization, plus a public rights-request contact
  `info@mcakosovo.org`. It does not establish a numeric retention schedule for
  these flows or identify a verified operational deletion owner.
- The user could not identify the retention/deletion owner or approved duration.
  Both remain **unconfirmed**. The privacy-notice approver is also unconfirmed.
- Local application privacy links use the configured link or WordPress policy
  URL; local WordPress policy resolves to `/privacy-policy/`. Published policy
  presence alone is not proof that it accurately describes every data flow.

## External follow-up outside the completed local scope

1. MCA must name the retention/deletion operator and privacy-notice approver, and
   approve periods covering submissions, applications, mail, CSV and backups.
2. Staging administrator can reactivate the deliberately disabled CF7 plugin and
   confirm each configured shortcode resolves to the intended form there.
3. Host/plugin owners must confirm production mail/storage integrations, trusted
   proxy settings and WAF/anti-spam rules for custom AJAX and CF7 separately.
4. Implement an approved deletion policy only after these decisions; do not infer
   a period or delete records as part of this verification.

Local verification and documentation are complete. No functional code changes
were required for this verification task. Operational ownership, staging parity
after CF7 reactivation, and production server-side enforcement remain unconfirmed;
the full cross-environment V-02 acceptance is not claimed as complete.
