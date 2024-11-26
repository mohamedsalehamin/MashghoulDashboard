import React, {useEffect, useState} from "react";
import AgoraUIKit, {layout} from "agora-react-uikit";

export default ({token, channel, partner}) => {
    const [chatVisible, setChatVisible] = useState(true);
    const rtcProps = {
        appId: 'e2c47794663143b6b31416d1adc35f77',
        channel: channel,
        token: token,
        uid: 0,
        enableAudio:false,
        enableVideo:false,
        layout: layout.grid,
        styleProps: {
            heading: {textAlign: 'center', marginBottom: 0},
            videoContainer: {display: 'flex', flexDirection: 'column', flex: 1},
            nav: {display: 'flex', justifyContent: 'space-around'},
            btn: {
                backgroundColor: 'black',
                cursor: 'pointer',
                borderRadius: 5,
                padding: 5,
                color: 'black',
                fontSize: 20
            },
            // localBtnContainer: {backgroundColor: 'black'}
        }
    };

    const callbacks = {
        EndCall: function () {
            setChatVisible(false);
            const event = new CustomEvent('sessionClosed');
            document.dispatchEvent(event);
        },
    };

    return chatVisible && (
        <div>
            <div style={{display: 'flex', width: '100%', height: '80vh'}}>
                <AgoraUIKit rtcProps={rtcProps} callbacks={callbacks}
                            styleProps={rtcProps.styleProps}
                />
            </div>
        </div>
    );

}
