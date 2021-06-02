export interface IComment {
    id: string;
    content: string;
    user: {
        id: string;
        username: string;
        profilePicture?: string;
    }
}
