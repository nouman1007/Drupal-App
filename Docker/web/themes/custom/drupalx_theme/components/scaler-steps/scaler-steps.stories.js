import './scaler-steps.scss';
import ScalerStepsTemplate from './scaler-steps.twig';


export default {
  title: 'Editorial/Scaler Steps Top',
  argTypes: {
    section_title: {
      description: 'The title of the scaler steps component',
      control: 'text'
    },
    section_subtitle: {
      description: 'The subtitle of the scaler steps component',
      control: 'text'
    },
    step_items: {
      description: 'Array of the scaler steps item content',
      control: 'object',
      type: {
        required: true
      }
    },
  }
};

export const ScalerSteps = ScalerStepsTemplate.bind({});
ScalerSteps.args = {
  section_title: 'SCALER steps',
  section_subtitle: 'The SCALER consists of three steps:',
  step_items: [
    {
      modifier: 'col-4 pt-4 px-6 pb-4',
      number: '1',
      heading: {
        title: 'Identifying evidence of effectiveness',
        heading_level: '3',
        modifier: 'step-title mb-2'
      },
      summary:
        'Use this checklist to assess whether your intervention has evidence of effectiveness. If evidence of the intervention’s effectiveness does not exist, such evidence must be built. Should the intervention be shown to be effective, the organization would get ready to scale.',
      modal_link: {
        url: '#start-scaler-step-1',
        text: 'Step 1',
      },
      modal: '<!-- Modal Step 1 --><div aria-hidden="true" aria-labelledby="Start SCALER" class="modal fade" id="start-scaler-step-1" role="dialog" tabindex="-1"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Starting SCALER</h5><button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div><div class="modal-body"><div class="modal-step-begin"><p>I want to start a new Step 1 from the beginning</p><div class="nav-button next-button"><a class="start button btn btn-primary rounded-pill" href="scaler-step1?reset=1#edit-overview-building-evidence">START</a></div></div><div class="modal-step-continue"><label for="scaler-link">I have started Step 1 and want to return to complete, edit, or review. Enter my saved unique web address:</label> <input aria-required="true" class="rounded-pill" id="scaler-link-1" name="scaler" placeholder="Enter unique web address" type="url"><button class="continue-1 button btn btn-primary rounded-pill" type="submit">CONTINUE</button></div></div></div></div></div>'
    },
    {
      modifier: 'col-4 pt-4 px-6 pb-4',
      number: '2',
      heading: {
        title: 'Building evidence of effectiveness',
        heading_level: '3',
        modifier: 'step-title mb-2'
      },
      summary:
        'If evidence of the intervention’s effectiveness does not exist, use this checklist as your organization seeks to build evidence of effectiveness for your intervention. Should the intervention be shown to be effective, the organization would get ready to scale.',
      modal_link: {
        url: '#start-scaler-step-2',
        text: 'Step 2'
      },
      modal: '<!-- Modal Step 2 --><div aria-hidden="true" aria-labelledby="Start SCALER" class="modal fade" id="start-scaler-step-2" role="dialog" tabindex="-1"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Starting SCALER</h5><button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div><div class="modal-body"><div class="modal-step-begin"><p>I want to start a new Step 2 from the beginning</p><div class="nav-button next-button"><a class="start button btn btn-primary rounded-pill" href="scaler-step2?reset=1#edit-overview-building-evidence">START</a></div></div><div class="modal-step-continue"><label for="scaler-link">I have started Step 2 and want to return to complete, edit, or review. Enter my saved unique web address:</label> <input aria-required="true" class="rounded-pill" id="scaler-link-2" name="scaler" placeholder="Enter unique web address" type="url"><button class="continue-2 button btn btn-primary rounded-pill" type="submit">CONTINUE</button></div></div></div></div></div>'
    },
    {
      modifier: 'col-4 pt-4 px-6 pb-6',
      number: '3',
      heading: {
        title: 'Getting ready to scale',
        heading_level: '3',
        modifier: 'step-title mb-2'
      },
      summary:
        'Use the checklists in this step to assess whether your organization and intervention are ready to scale. The checklists assess five conditions necessary to facilitate successful scaling: (1) Specifying the intervention, (2) Defining the target population, (3) Establishing implementation supports, (4) Having an enabling context, and (5) Establishing an implementation infrastructure.',
      modal_link: {
        url: '#start-scaler-step-3',
        text: 'Step 3'
      },
      modal: '<!-- Modal Step 3 --><div aria-hidden="true" aria-labelledby="Start SCALER" class="modal fade" id="start-scaler-step-3" role="dialog" tabindex="-1"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Starting SCALER</h5><button aria-label="Close" class="close" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div><div class="modal-body"><div class="modal-step-begin"><p>I want to start a new Step 3 from the beginning</p><div class="nav-button next-button"><a class="start button btn btn-primary rounded-pill" href="scaler-step3?reset=1#edit-determining-readiness-to-scale">START</a></div></div><div class="modal-step-continue"><label for="scaler-link">I have started Step 3 and want to return to complete, edit, or review. Enter my saved unique web address:</label> <input aria-required="true" class="rounded-pill" id="scaler-link-3" name="scaler" placeholder="Enter unique web address" type="url"><button class="continue-3 button btn btn-primary rounded-pill" type="submit">CONTINUE</button></div></div></div></div></div>'
    },
  ],
  bottom_title: 'Planning your next steps',
  bottom_text:
    'Use the discussion guide questions to identify next steps for strengthening your scaling readiness after completing the SCALER.',
  bottom_link: {
    url: '#',
    text: 'View Planning Your Next Steps Guide'
  }
};
