import './scaler-steps-bottom.scss';
import ScalerStepsBottomTemplate from './scaler-steps-bottom.twig';


export default {
  title: 'Editorial/Scaler Steps Bottom',
  argTypes: {
    bottom_title: {
      description: 'The title of the scaler steps bottom component',
      control: 'text'
    },
    bottom_subtitle: {
      description: 'The subtitle of the scaler steps bottom component',
      control: 'text'
    },
    bottom_link: {
      description: 'The link for the scaler steps bottom component',
      control: 'object'
    },
  }
};

export const ScalerStepsBottom = ScalerStepsBottomTemplate.bind({});
ScalerStepsBottom.args = {
  bottom_title: 'Planning your next steps',
  bottom_text:
    'Use the discussion guide questions to identify next steps for strengthening your scaling readiness after completing the SCALER.',
  bottom_link: {
    url: '#',
    text: 'View Planning Your Next Steps Guide'
  }
};
