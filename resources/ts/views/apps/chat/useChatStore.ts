import type {
  ChatContact,
  ChatContactWithChat,
  ChatMessage,
  ChatOut,
} from "@/@fake-db/types";
import axios from "@axios";
import type { ActiveChat } from "./useChat";

interface State {
  chatsContacts: ChatContactWithChat[];
  contacts: ChatContact[];
  profileUser: ChatContact | undefined;
  activeChat: ActiveChat;
}

export const useChatStore = defineStore("chat", {
  // ℹ️ arrow function recommended for full type inference
  state: (): State => ({
    contacts: [],
    chatsContacts: [],
    profileUser: undefined,
    activeChat: null,
  }),
  actions: {
    async fetchChatsAndContacts(q: string) {
      const { data } = await axios.post("/chats/list", {
        search: q,
      });
    
      const { chats, contacts, profileUser } = data.data;
      const profileUserData: ChatContact = profileUser;
      this.chatsContacts = chats;
      this.contacts = contacts;
      this.profileUser = profileUserData;
    },

    async getChat(userId: ChatContact["id"]) {
      //! Need to change this
      const { data } = await axios.post(`/chats/chat/${userId}`);
                      await axios.get(`/chats/message-seen/${userId}`);
    
      this.activeChat = data.data;
    },
    async chatClear(userId: ChatContact["id"]) {
      //! Need to change this

      const clear = await axios.get(`/chats/chat-clear/${userId}`);
      //! Need to check after clear chat 
      this.getChat(userId);
      this.fetchChatsAndContacts("");
      // this.activeChat = null
    },

    async sendMsg(message: ChatMessage["message"]) {
      const senderId = this.profileUser?.id;
      const input = {
        sender_id: this.activeChat?.contact.id,
        receiver_id: senderId,
        message: message,
        is_sent: true,
        is_attachment: true,
      };
      const { data } = await axios.post(`/chats/create`, input);
      // console.log("return responsactiveChate", data);

      const { msg, chat }: { msg: ChatMessage; chat: ChatOut } = data.data;

      // ? If it's not undefined => New chat is created (Contact is not in list of chats)

      if (chat !== undefined) {
        // eslint-disable-next-line @typescript-eslint/no-non-null-assertion

        const activeChat = this.activeChat!;

        this.chatsContacts.push({
          ...activeChat.contact,
          chat: {
            id: chat.id,
            lastMessage: [],
            unseenMsgs: 0,
            messages: [msg],
          },
        });

        if (this.activeChat) {
          this.activeChat.chat = {
            id: chat.id,
            messages: chat.messages,
            unseenMsgs: 0,
            userId: this.activeChat?.contact.id,
          };
        }
      } else {
        // console.log("active chat data", this.activeChat);
        this.activeChat?.chat?.messages.push(msg);
        // console.log(" after active chat data", this.activeChat);
      }
      // console.log("chats contacts", this.chatsContacts);

      // Set Last Message for active contact
      const contact = this.chatsContacts.find((c) => {
        if (this.activeChat) return c.id === this.activeChat.contact.id;

        return false;
      }) as ChatContactWithChat;
      
      // console.log("chats contacts", contact);
      if(contact.chat.lastMessage.length > 0){
        contact.chat.lastMessage = msg;
      }
    },
  },
});
