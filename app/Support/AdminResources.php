<?php

namespace App\Support;

use App\Models\{Activity, Award, Contact, ContactMessage, CrmTask, Lead, Opportunity, Organization, OutreachEmail, Project, Qualification, Service, SiteSetting};

class AdminResources
{
    public static function all(): array
    {
        $status = fn (array $options, string $default) => ['type' => 'select', 'options' => $options, 'default' => $default, 'required' => true];
        $relation = fn (string $model, string $label) => ['type' => 'relation', 'model' => $model, 'label_field' => $label];
        $text = fn (bool $required = false) => ['type' => 'text', 'required' => $required];

        return [
            'projects' => ['label' => 'Projects', 'model' => Project::class, 'title' => 'title', 'columns' => ['title','category','type','is_featured','is_published'], 'fields' => [
                'title' => $text(true), 'slug' => $text(), 'type' => $status(['publication'=>'Publication / flipbook','poster'=>'Poster or campaign','social'=>'Social media design','branding'=>'Brand identity','other'=>'Other'], 'publication'),
                'category' => $text(true), 'summary' => ['type'=>'textarea','required'=>true], 'url' => ['type'=>'url'], 'image_path' => ['type'=>'file','accept'=>'image/*','directory'=>'projects/covers'],
                'pdf_path' => ['type'=>'file','accept'=>'application/pdf','directory'=>'projects/publications'], 'gallery' => ['type'=>'files','accept'=>'image/*','directory'=>'projects/gallery'],
                'client'=>$text(), 'organization'=>$text(), 'status'=>$text(), 'published_at'=>['type'=>'date'], 'challenge'=>['type'=>'textarea'], 'solution'=>['type'=>'textarea'], 'responsibilities'=>['type'=>'textarea'],
                'technologies'=>['type'=>'tags'], 'accent'=>$status(['plum'=>'Plum','coral'=>'Coral','sage'=>'Sage','gold'=>'Gold'],'plum'), 'sort_order'=>['type'=>'number','default'=>0],
                'is_featured'=>['type'=>'checkbox'], 'is_downloadable'=>['type'=>'checkbox'], 'is_published'=>['type'=>'checkbox','default'=>true],
            ]],
            'services' => ['label'=>'Services','model'=>Service::class,'title'=>'title','columns'=>['title','description','sort_order','is_published'],'fields'=>[
                'title'=>$text(true),'description'=>['type'=>'textarea','required'=>true],'tags'=>['type'=>'tags'],'sort_order'=>['type'=>'number','default'=>0],'is_published'=>['type'=>'checkbox','default'=>true],
            ]],
            'awards' => ['label'=>'Awards & recognition','model'=>Award::class,'title'=>'title','columns'=>['title','placement','issuer','awarded_at','is_featured','is_published'],'fields'=>[
                'title'=>$text(true),'placement'=>$text(),'issuer'=>$text(true),'awarded_at'=>['type'=>'date'],'recognition_for'=>$text(),'description'=>['type'=>'textarea'],'image_path'=>['type'=>'file','accept'=>'image/*','directory'=>'awards'],
                'proof_url'=>['type'=>'url'],'sort_order'=>['type'=>'number','default'=>0],'is_featured'=>['type'=>'checkbox'],'is_published'=>['type'=>'checkbox','default'=>true],
            ]],
            'qualifications' => ['label'=>'Qualifications','model'=>Qualification::class,'title'=>'title','columns'=>['title','institution','credential_type','end_year','is_published'],'fields'=>[
                'title'=>$text(true),'institution'=>$text(true),'credential_type'=>$text(),'start_year'=>['type'=>'number'],'end_year'=>['type'=>'number'],'description'=>['type'=>'textarea'],'credential_url'=>['type'=>'url'],'sort_order'=>['type'=>'number','default'=>0],'is_published'=>['type'=>'checkbox','default'=>true],
            ]],
            'site-settings' => ['label'=>'Site content','model'=>SiteSetting::class,'title'=>'name','singleton'=>true,'columns'=>['name','professional_title','updated_at'],'fields'=>[
                'name'=>$text(true),'professional_title'=>$text(true),'availability'=>$text(),'hero_heading'=>$text(true),'hero_accent'=>$text(true),'hero_intro'=>['type'=>'textarea','required'=>true],
                'about_heading'=>$text(true),'profile_photo'=>['type'=>'file','accept'=>'image/*','directory'=>'profile'],'about_body'=>['type'=>'textarea','required'=>true],'email'=>['type'=>'email','required'=>true],
                'linkedin_url'=>['type'=>'url'],'instagram_url'=>['type'=>'url'],'behance_url'=>['type'=>'url'],'github_url'=>['type'=>'url'],'upwork_url'=>['type'=>'url'],'contact_heading'=>$text(true),'contact_body'=>['type'=>'textarea','required'=>true],
            ]],
            'contact-messages' => ['label'=>'Contact messages','model'=>ContactMessage::class,'title'=>'subject','readonly'=>['name','email','subject','message'],'columns'=>['name','email','subject','status','created_at'],'fields'=>[
                'name'=>$text(),'email'=>['type'=>'email'],'subject'=>$text(),'message'=>['type'=>'textarea'],'status'=>$status(['new'=>'New','replied'=>'Replied','archived'=>'Archived'],'new'),
            ]],
            'organizations' => ['label'=>'Organizations','model'=>Organization::class,'title'=>'name','columns'=>['name','industry','country','status','email'],'fields'=>[
                'name'=>$text(true),'industry'=>$text(),'country'=>$text(),'website'=>['type'=>'url'],'email'=>['type'=>'email'],'phone'=>$text(),'status'=>$status(['prospect'=>'Prospect','active'=>'Active client','past'=>'Past client','do_not_contact'=>'Do not contact'],'prospect'),'notes'=>['type'=>'textarea'],
            ]],
            'contacts' => ['label'=>'Contacts','model'=>Contact::class,'title'=>'first_name','columns'=>['first_name','last_name','organization.name','job_title','email','status'],'fields'=>[
                'organization_id'=>$relation(Organization::class,'name'),'first_name'=>$text(true),'last_name'=>$text(),'email'=>['type'=>'email'],'phone'=>$text(),'job_title'=>$text(),'status'=>$status(['prospect'=>'Prospect','contacted'=>'Contacted','active'=>'Active','do_not_contact'=>'Do not contact'],'prospect'),'notes'=>['type'=>'textarea'],
            ]],
            'leads' => ['label'=>'Leads','model'=>Lead::class,'title'=>'title','columns'=>['title','organization.name','source','status','estimated_value','next_follow_up_at'],'fields'=>[
                'title'=>$text(true),'organization_id'=>$relation(Organization::class,'name'),'contact_id'=>$relation(Contact::class,'first_name'),'source'=>$status(['website'=>'Website','referral'=>'Referral','leo_lions'=>'Leo / Lions network','instagram'=>'Instagram','linkedin'=>'LinkedIn','behance'=>'Behance','direct_outreach'=>'Direct outreach','manual'=>'Manual'],'manual'),
                'status'=>$status(['new'=>'New','researching'=>'Researching','contacted'=>'Contacted','replied'=>'Replied','qualified'=>'Qualified','unqualified'=>'Unqualified','converted'=>'Converted'],'new'),'estimated_value'=>['type'=>'number'],'next_follow_up_at'=>['type'=>'datetime-local'],'description'=>['type'=>'textarea'],
            ]],
            'opportunities' => ['label'=>'Opportunities','model'=>Opportunity::class,'title'=>'name','columns'=>['name','organization.name','stage','amount','probability','expected_close_date'],'fields'=>[
                'name'=>$text(true),'organization_id'=>$relation(Organization::class,'name'),'lead_id'=>$relation(Lead::class,'title'),'contact_id'=>$relation(Contact::class,'first_name'),'stage'=>$status(['researching'=>'Researching','contacted'=>'Contacted','discovery'=>'Discovery','proposal'=>'Proposal','negotiation'=>'Negotiation','won'=>'Won','lost'=>'Lost'],'researching'),
                'amount'=>['type'=>'number'],'probability'=>['type'=>'number','default'=>10],'expected_close_date'=>['type'=>'date'],'description'=>['type'=>'textarea'],'lost_reason'=>['type'=>'textarea'],
            ]],
            'activities' => ['label'=>'Activities','model'=>Activity::class,'title'=>'subject','columns'=>['type','subject','contact.full_name','occurred_at'],'fields'=>[
                'type'=>$status(['email'=>'Email','call'=>'Call','meeting'=>'Meeting','note'=>'Note'],'note'),'subject'=>$text(true),'contact_id'=>$relation(Contact::class,'first_name'),'lead_id'=>$relation(Lead::class,'title'),'opportunity_id'=>$relation(Opportunity::class,'name'),'occurred_at'=>['type'=>'datetime-local'],'details'=>['type'=>'textarea'],
            ]],
            'crm-tasks' => ['label'=>'Follow-up tasks','model'=>CrmTask::class,'title'=>'title','columns'=>['title','status','priority','due_at','completed_at'],'fields'=>[
                'title'=>$text(true),'contact_id'=>$relation(Contact::class,'first_name'),'lead_id'=>$relation(Lead::class,'title'),'opportunity_id'=>$relation(Opportunity::class,'name'),'status'=>$status(['todo'=>'To do','in_progress'=>'In progress','done'=>'Done','cancelled'=>'Cancelled'],'todo'),'priority'=>$status(['low'=>'Low','normal'=>'Normal','high'=>'High','urgent'=>'Urgent'],'normal'),'due_at'=>['type'=>'datetime-local'],'completed_at'=>['type'=>'datetime-local'],'description'=>['type'=>'textarea'],
            ]],
            'outreach-emails' => ['label'=>'Email queue','model'=>OutreachEmail::class,'title'=>'subject','columns'=>['organization.name','recipient_email','subject','status','follow_up_at','sent_at'],'fields'=>[
                'organization_id'=>$relation(Organization::class,'name'),'contact_id'=>$relation(Contact::class,'first_name'),'lead_id'=>$relation(Lead::class,'title'),'recipient_name'=>$text(),'recipient_email'=>['type'=>'email','required'=>true],'subject'=>$text(true),'body'=>['type'=>'textarea','required'=>true],
                'status'=>$status(['draft'=>'Draft','ready'=>'Ready for review','approved'=>'Approved','sent'=>'Sent','replied'=>'Replied','bounced'=>'Bounced','do_not_contact'=>'Do not contact'],'draft'),'sent_at'=>['type'=>'datetime-local'],'follow_up_at'=>['type'=>'datetime-local'],'notes'=>['type'=>'textarea'],
            ]],
        ];
    }

    public static function get(string $resource): array
    {
        abort_unless(isset(self::all()[$resource]), 404);
        return self::all()[$resource];
    }
}
