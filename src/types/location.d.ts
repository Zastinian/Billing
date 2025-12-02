import type Node from "./node";

export default interface Location {
  attributes: {
    id: number;
    short: string;
    relationships: {
      nodes: {
        data: Node[];
      };
    };
  };
}
