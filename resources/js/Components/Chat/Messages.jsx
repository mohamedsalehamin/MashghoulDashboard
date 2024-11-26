import Header from "./Header.jsx";
import Message from "./Message.jsx";
import React, {useRef} from "react";

export default ({messages,partner,client}) => {
    function useChatScroll(dep) {
        const ref = useRef();
        React.useEffect(() => {
            if (ref.current) {
                ref.current.scrollTop = ref.current.scrollHeight;
            }
        }, [dep]);
        return ref;
    }
    const ref = useChatScroll(messages)

    return <>
        <div className="messages-content" ref={ref}>
            <div className="messages-list">
                {messages.map((message, index) => (
                    <Message message={message} partner={partner} client={client} key={index}/>
                ))}

            </div>
        </div>


    </>
}
