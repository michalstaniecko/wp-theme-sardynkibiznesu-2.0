import * as CookieConsent from 'vanilla-cookieconsent';
import data from './data.json';

window.addEventListener( 'load', () => {
    console.log( 'ihumbak-cookies.js' );
    CookieConsent.run( {
        categories: {
            necessary: {
                enabled: true,  // this category is enabled by default
                readOnly: true  // this category cannot be disabled
            },
            analytics: {
                enabled: false
            },
            marketing: {
                enabled: false
            }
        },

        guiOptions: {
            consentModal: {
                layout: 'bar',
            }
        },

        language: {
            default: 'pl',
            translations: {
                pl: {
                    consentModal: {
                        title: data.consentModal.title,
                        description: data.consentModal.description,
                        acceptAllBtn: data.consentModal.acceptAllBtn,
                        acceptNecessaryBtn: data.consentModal.acceptNecessaryBtn,
                        showPreferencesBtn: data.consentModal.showPreferencesBtn
                    },
                    preferencesModal: {
                        title: data.preferencesModal.title,
                        acceptAllBtn: data.preferencesModal.buttons.acceptAll,
                        acceptNecessaryBtn: data.preferencesModal.buttons.rejectAll,
                        savePreferencesBtn: data.preferencesModal.buttons.savePreferences,
                        closeIconLabel: data.preferencesModal.closeIconLabel,
                        sections: [
                            {
                                title: data.preferencesModal.sections[0].title,
                                description: data.preferencesModal.sections[0].description,
                                linkedCategory: data.preferencesModal.sections[0].linkedCategory
                            },
                            {
                                title: data.preferencesModal.sections[1].title,
                                description: data.preferencesModal.sections[1].description,
                                linkedCategory: data.preferencesModal.sections[1].linkedCategory
                            },
                            // {
                            //     title: data.preferencesModal.sections[2].title,
                            //     description: data.preferencesModal.sections[2].description,
                            //     linkedCategory: data.preferencesModal.sections[2].linkedCategory
                            // },
                            {
                                title: data.preferencesModal.sections[3].title,
                                description: data.preferencesModal.sections[3].description,
                            }
                        ]
                    }
                }
            }
        }
    } )
} )
