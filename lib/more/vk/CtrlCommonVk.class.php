<?php

class CtrlCommonVk extends CtrlCommon {

  function action_asd() {
    $this->hasOutput = false;
    print '<table><tr><td valign="top">';
    prr(VkSite::friends()->getFriends());
    print '</td><td valign="top">';
    prr(VkSite::msgs()->getSentUserIds());
    print '</td></tr></table>';
    //$this->json['friends'] = VkSite::friends()->getFriends();
    //$this->json['sentUsers'] = VkSite::msgs()->getSentUserIds();
  }
  
  function action_json_info() {
    $this->json['friends'] = VkSite::friends()->getFriends();
    $this->json['sentUsers'] = VkSite::msgs()->getSentUserIds();
    if (($id = VkSite::msgs()->getLastSentUserId()) !== false) {
      $this->json['lastSentUser'] = [
        'id' => $id,
        'name' => VkSite::userInfo()->getName($id)
      ];
    }
  }
  
  function action_json_getLastStep() {
    $this->json = O::get('PartialJobVkMsgs')->getLastStep();
  }
  
  function action_json_pjSend() {
    $this->json = O::get('PartialJobVkMsgs')->setJobsData([
      'message' => $this->req->rq('message')
    ])->makeStep($this->req->rq('step'));
  }

}
