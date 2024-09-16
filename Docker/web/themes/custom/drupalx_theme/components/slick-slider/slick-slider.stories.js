import './slick-slider.scss';
import SlickSliderTemplate from './slick-slider.twig';

export default {
  title: 'Editorial/Slick Slider',
  argTypes: {
    items: {
      description: 'Define the array of carousel items',
      control: 'object',
      type: {
        required: true
      }
    }
  }
};

export const SlickSlider = SlickSliderTemplate.bind({});

SlickSlider.args = {
  "items": [
    {
      "image": "",
      "title": "First slide title",
      "body": "First slide content"
    },
    {
      "image": "",
      "title": "Second slide title",
      "body": "Second slide content"
    },
    {
      "image": "",
      "title": "Third slide title",
      "body": "Third slide content"
    },
    {
      "image": "",
      "title": "Fourth slide title",
      "body": "Fourth slide content"
    },
    {
      "image": "",
      "title": "Fifth slide title",
      "body": "Fifth slide content"
    },
    {
      "image": "",
      "title": "Sixth slide title",
      "body": "Sixth slide content"
    },
    {
      "image": "",
      "title": "Seventh slide title",
      "body": "Seventh slide content"
    }
  ]
}